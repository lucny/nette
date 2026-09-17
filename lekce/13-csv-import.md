# Lekce 13 – CSV import: soubor není důvěryhodný vstup

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 12](12-prihlaseni-security.md) · **Výsledek:** přihlášený uživatel nahraje CSV, aplikace jej po řádcích ověří, vloží nebo aktualizuje produkty a zřetelně vypíše chyby.

> **🎯 Cíl lekce**
>
> Dokážete popsat cestu jednoho řádku od nahraného souboru až do tabulky `product`. Rozlišíte technický úspěch uploadu od platnosti obsahu řádku a vysvětlíte, proč parser nepatří do presenteru.

## Problém, který řešíme

Ručně přidat jeden produkt je přehledné. Přidat dvacet položek stejným způsobem už je zdlouhavé a chybové. CSV (*comma-separated values*) je prostý textový tabulkový formát; v tomto projektu jsou sloupce odděleny středníkem, protože česká desetinná čísla často používají čárku.

Soubor však přišel z prohlížeče. I když se jmenuje `produkty.csv`, může být příliš velký, nečitelný, mít jinou hlavičku, prázdný kód nebo zápornou cenu. Žádná z těchto chyb nesmí skončit nepozorovaným zápisem do databáze.

```text
prohlížeč ─upload─> ImportPresenter ─obsah souboru─> ProductImporter
                                                      │
      výsledek <── Latte <── pole počtů a chyb <──────┤
                                                      ▼
  validace uploadu → hlavička → řádek → ProductInputValidator → UPSERT → MySQL
```

> **🧠 Nejdřív přemýšlej**
>
> Pokud server zkontroluje jen příponu `.csv`, co brání uživateli přejmenovat libovolný soubor? A pokud zkontroluje jen velikost souboru, ví už něco o ceně na třetím řádku?

## Co je PHP a co přidává Nette

| PHP | Nette a návrh aplikace |
|---|---|
| `fopen()`, `fwrite()`, `rewind()` a `fgetcsv()` pracují se streamem textu | `FileUpload` reprezentuje nahraný soubor a umí zjistit, zda upload vůbec proběhl |
| `while` čte řádek za řádkem; `array_combine()` spojí názvy sloupců s hodnotami | `addUpload()` nastaví povinné pole, limit velikosti a povolené typy formuláře |
| `const` chrání očekávanou hlavičku před náhodnou změnou | `ProductImporter` je služba: presenter přebírá HTTP formulář, služba zpracuje data |
| návratové pole nese chyby formátu i jednotlivých řádků; výjimka chrání selhání dočasného streamu | `Explorer::transaction()` obaluje databázové změny úspěšně zpracovaných řádků |

### Důležitý rozdíl: upload ≠ platná data

`$upload->isOk()` znamená jen to, že PHP přijalo soubor bez technické chyby uploadu. Neznamená to, že je v souboru správná hlavička nebo že `price` je nezáporné číslo. Proto má import několik samostatných bran:

1. formulář přijme jen povinný soubor do 2 MB;
2. presenter ověří stav `FileUpload`;
3. importer přesně ověří hlavičku;
4. každý řádek znormalizuje a předá stejnému `ProductInputValidator`, který znáte z formuláře produktu;
5. až platný řádek smí do repository a databáze.

## Nejdřív si přečtěte vstupy a očekávaný výsledek

**📄 Úplný soubor:** [`database/sample-products.csv`](../database/sample-products.csv)

```csv
active;code;name;description;stock;price
true;NB-001;Notebook 15;Aktualizovaný popis z importu.;15;18490.00
true;CA-009;USB-C kabel;Demonstrační nový produkt.;25;249.00
false;ST-010;Stojan na monitor;Ukázkový neaktivní produkt.;0;899.00
```

První řádek není produkt. Je to **smlouva o pořadí a názvech šesti sloupců**. Kód ji porovnává přesně; `aktivni` ani prohozené `price;stock` nejsou „téměř správně“.

**📄 Úplný soubor:** [`database/invalid-products.csv`](../database/invalid-products.csv)

```csv
active;code;name;description;stock;price
ano;bad code;;Příliš krátký řádek;sklad?;-10
true;OK-011;Validní řádek;Tento řádek se zpracuje.;2;499,90
```

První datový řádek má několik chyb. Druhý je záměrně platný a cena s čárkou ukazuje, že import před validací provede malou, předvídatelnou normalizaci.

## Kód pod lupou

### 1. Formulář přijme soubor, ale neparsuje ho

**📄 Přesná citace z:** `app/Presentation/Import/ImportPresenter.php`, metody `createComponentImportForm()`

```php
$form->addUpload('csv', 'CSV soubor')
	->setRequired('Vyber CSV soubor.')
	->addRule(Form::MaxFileSize, 'Soubor může mít nejvýše 2 MB.', 2 * 1024 * 1024)
	->addRule(Form::MimeType, 'Povoleno je pouze CSV nebo textový soubor.', ['text/csv', 'text/plain', 'application/vnd.ms-excel']);
$form->addSubmit('send', 'Spustit import');
$form->onSuccess[] = $this->importFormSucceeded(...);
```

`2 * 1024 * 1024` je výpočet 2 MiB v bajtech. `MimeType` je rozumná první brzda pro běžného uživatele, ale typ posílá klient; není to důkaz bezpečnosti obsahu. Proto následují další kontroly na serveru.

**📄 Přesná citace z:** `app/Presentation/Import/ImportPresenter.php`, metody `importFormSucceeded()`

```php
/** @var FileUpload $upload */
$upload = $data->csv;
if (!$upload->isOk()) {
	$form->addError('Nahrání souboru selhalo.');
	return;
}

$this->template->result = $this->importer->importCsv($upload->getContents() ?? '');
```

Operátor `??` říká: „je-li výsledek `getContents()` `null`, použij prázdný řetězec“. Presenter zde úmyslně nedělá `fgetcsv()` ani SQL. Jeho odpovědnost končí předáním obsahu službě a zobrazením výsledku.

### 2. Importer otevře dočasný stream a vrátí výsledek pro špatnou hlavičku

**📄 Přesná citace z:** `app/Model/Product/ProductImporter.php`

```php
private const HEADER = ['active', 'code', 'name', 'description', 'stock', 'price'];

$stream = fopen('php://temp', 'r+');
if ($stream === false) {
	throw new \RuntimeException('Nepodařilo se otevřít dočasný proud.');
}
fwrite($stream, $contents);
rewind($stream);

$header = fgetcsv($stream, separator: ';');
if ($header === false) {
	fclose($stream);
	return ['processed' => 0, 'inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => ['Soubor je prázdný.']];
}
$header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
if ($header !== self::HEADER) {
	fclose($stream);
	return ['processed' => 0, 'inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => ['Hlavička musí být: ' . implode(';', self::HEADER)]];
}
```

`php://temp` je dočasný stream v PHP, takže importer pracuje s obsahem, nikoli s veřejnou cestou souboru. `rewind()` vrátí kurzor na začátek před čtením. `fgetcsv()` vrací pole hodnot nebo `false`; cyklus proto může bezpečně skončit na `false`.

Řetězec `\xEF\xBB\xBF` je UTF-8 BOM – neviditelná značka, kterou někdy na začátek vloží tabulkový editor. Kdyby zůstala, první název sloupce by nebyl přesně `active` a srozumitelná kontrola hlavičky by správně selhala. PHP samo tento BOM v `fgetcsv()` neodstraňuje. Prázdný soubor a chybná hlavička zde nejsou technická výjimka: importer vrátí stejné výsledkové pole jako po běžném importu, takže šablona může uživateli zobrazit konkrétní chybu.

> **⚠️ Pozor na verzi PHP**
>
> Projekt cílí na PHP 8.2+. Aktuální dokumentace PHP upozorňuje, že od PHP 8.4 je spoléhání na výchozí hodnotu parametru `escape` u `fgetcsv()` zastaralé. Při budoucím rozšíření importeru proto ověřte aktuální signaturu funkce a případně `escape` nastavte výslovně. Neměňte jej mechanicky jen proto, že to stojí v obecné ukázce na Internetu.

### 3. Jeden řádek se normalizuje, ověří a teprve pak uloží

**📄 Zkrácený, doslovně převzatý fragment z:** `app/Model/Product/ProductImporter.php`, uvnitř transakce

```php
while (($row = fgetcsv($stream, separator: ';')) !== false) {
	$line++;
	if ($row === [null] || (count($row) === 1 && trim((string) $row[0]) === '')) {
		continue;
	}
	$result['processed']++;
	if (count($row) !== count(self::HEADER)) {
		$result['skipped']++;
		$result['errors'][] = "Řádek {$line}: očekáváno " . count(self::HEADER) . ' sloupců.';
		continue;
	}

$data = array_combine(self::HEADER, array_map('trim', $row));
$data['active'] = filter_var($data['active'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
$data['stock'] = filter_var($data['stock'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$data['price'] = str_replace(',', '.', $data['price']);
$errors = $this->validator->validate($data);
```

`array_combine(self::HEADER, $row)` změní například pole na asociativní pole s klíči `code`, `name` a dalšími. `trim()` odstraní mezery na začátku a konci. `filter_var(..., FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE)` vrátí celé číslo, nebo `null`; validátor proto pozná chybný sklad. Samostatná kontrola pro `active` navíc nepovolí nic jiného než `true` a `false`.

Validace se neopisuje. Stejná služba `ProductInputValidator` chrání ruční formulář i CSV. To je konkrétní přínos oddělené odpovědnosti: pravidlo o formátu kódu existuje na jednom místě. Všimněte si však rozdílu cest: formulář v `ProductPresenter` kód před validací převádí na velká písmena, CSV nikoli. CSV s `nb-001` tedy aktuální validátor odmítne. Je to přesný popis současného řešení a dobrý podklad pro pozdější refactoring, ne vlastnost, kterou si smíme domyslet.

### 4. UPSERT je vědomé rozhodnutí podle unikátního kódu

Importer po úspěšné validaci zavolá `ProductRepository::upsert()`. Repository nejprve hledá produkt podle `code`, pak volá buď `create()`, nebo `update()`.

```text
platný řádek
     │
     ├─ code v databázi není ───────────> INSERT / create()
     └─ code už v databázi existuje ────> UPDATE / update()
```

To je zde zvolený význam UPSERTu. Na úrovni MySQL jej navíc jistí `UNIQUE` index nad `product.code` z [lekce 7](07-mysql-schema.md). Nezaměňujte však význam transakce:

- chyba **formátu řádku** je do výsledku zapsána a řádek se přeskočí;
- platné řádky se zpracují v databázové transakci;
- neočekávaná databázová výjimka transakci přeruší a Nette Database změny v ní vrátí zpět.

Import tedy není „všechno, nebo nic“ pro každou uživatelskou chybu řádku. Je to záměrný výukový kompromis: student vidí všechny nalezené chyby a platné řádky přesto mohou projít.

## Postup krok za krokem

1. Přihlaste se výukovým účtem z [README](../README.md#3-vytvořte-výukový-účet) a otevřete `/import`.
2. V novém panelu otevřete [`database/sample-products.csv`](../database/sample-products.csv). Porovnejte jeho první řádek s `HEADER` ve `ProductImporter` – včetně pořadí.
3. Zvolte soubor `sample-products.csv` a stiskněte **Spustit import**.
4. Do zápisníku si zaznamenejte čtyři hodnoty výsledku: `processed`, `inserted`, `updated`, `skipped`. Vysvětlete, proč se při prvním běhu může `NB-001` aktualizovat, zatímco jiné kódy se vloží.
5. Otevřete `/product` a vyhledejte `CA-009`. Tím ověříte výsledek v databázovém seznamu, nejen hlášku formuláře.
6. Nahrajte stejný soubor ještě jednou. Porovnejte počet `inserted` a `updated`; podívejte se, zda se změnil `updated_at`.
7. Nakonec nahrajte [`database/invalid-products.csv`](../database/invalid-products.csv). U prvního datového řádku najděte každou uvedenou chybu. Ověřte, že `OK-011` se přesto dostal do seznamu.

> **🔎 Co má být pozorovatelné**
>
> U chybného CSV neuvidíte neurčité „import selhal“. Uvidíte číslo řádku a důvod přeskočení. U platného řádku změnu ověříte v seznamu produktů. To jsou dva nezávislé důkazy správného chování.

## Experiment: změna dat bez změny hlavičky

1. Zkopírujte si `sample-products.csv` mimo repozitář, aby nezmizel referenční vzor.
2. V kopii změňte název produktu s kódem `CA-009`, ale hlavičku nechte beze změny.
3. Importujte kopii a vyhledejte `CA-009` v seznamu.
4. Zapište hypotézu předem: „Protože kód existuje, očekávám …“
5. Porovnejte předpoklad s výsledkem a vysvětlete, proč nedošlo ke vzniku druhého produktu se stejným kódem.

## Samostatný úkol

Navrhněte, **bez úpravy hotového řešení**, pravidlo pro duplicitní `code` dvakrát v jednom CSV souboru. Vyberte jednu variantu:

- druhý řádek přeskočit a uvést chybu;
- druhý řádek považovat za aktualizaci prvního;
- odmítnout celý soubor před databázovou změnou.

Ke své volbě doplňte: kde by se udržovala informace o již viděných kódech, jak by se změnil výsledek importu a co by znamenala transakce. Neřešte to JavaScriptem – rozhodnutí musí být na serveru.

## Minikvíz

1. Co zaručuje `$upload->isOk()`? **Že technicky proběhl upload, ne platnost obsahu CSV.**
2. Proč je hlavička součástí smlouvy? **Určuje názvy i pořadí hodnot, které kód očekává.**
3. Kde se opakovaně používá pravidlo pro kód produktu? **V `ProductInputValidator`, pro formulář i import.**
4. Znamená `updated = 1` vždy, že vznikl nový produkt? **Ne, znamená aktualizaci existujícího kódu.**
5. Kam patří parsování CSV? **Do služby `ProductImporter`, ne do presenteru ani Latte.**

## Kontrolní body a ověřené zdroje

- [ ] Platný vzor zpracuje řádky a v seznamu lze dohledat `CA-009`.
- [ ] Druhé nahrání ukáže rozdíl mezi vložením a aktualizací.
- [ ] Chybný vzor vypíše chyby s číslem řádku a platný `OK-011` projde.
- [ ] Dokážete vysvětlit všech pět bran importu od uploadu po databázi.

Pro další čtení použijte oficiální dokumentaci [Nette Forms v presenteru](https://doc.nette.org/en/forms/in-presenter), [Nette Database: transakce](https://doc.nette.org/en/database/core#toc-transactions) a [PHP `fgetcsv()`](https://www.php.net/manual/en/function.fgetcsv.php). Informace o CSV čtěte vždy s verzí PHP, kterou opravdu používáte.

## Stav projektu po lekci

Administrace umí importovat CSV jen po přihlášení. Kontroluje upload, přesnou hlavičku, počet sloupců a hodnoty řádků; platné položky ukládá přes existující repository. Příště stejná repository data nabídne jinému typu klienta: jako JSON odpověď místo HTML stránky.
