# Lekce 13 – CSV import

**Čas:** 2 × 45 minut  
**Výchozí stav:** přihlášená administrace s tabulkou `product`.

## Co dnes vytvoříme

Upload CSV v `ImportPresenter`, samostatnou službu `ProductImporter`, validaci řádků a přehled vložených, aktualizovaných a přeskočených řádků.

## Co se naučíme

- oddělit upload od parsování a databázové logiky,
- ověřit soubor, hlavičku, počet sloupců a datové typy,
- vysvětlit transakci a UPSERT,
- neignorovat chybné řádky potichu.

## Kde jsme skončili

Produkt lze měnit ručně. Pro více položek potřebujeme dávkový vstup, který je stále nedůvěryhodný.

## Nové pojmy

upload, stream, CSV, parser, transakce, UPSERT, INSERT, UPDATE, hlavička.

## PHP princip

`fgetcsv()` čte řádek po řádku. `array_combine()` sváže hlavičku s hodnotami. `filter_var()` převádí a kontroluje bool/int. Výjimka by neměla nahradit uživatelský seznam chyb.

## Nette princip

`addUpload()` poskytne `FileUpload`, který kontroluje stav uploadu a omezení formuláře. `ProductImporter` je služba, ne presenter; presenter pouze přijme soubor, zavolá službu a zobrazí výsledek.

## Jak to funguje

```text
upload → velikost/typ → header → řádky → validace → transakce → UPSERT → výsledek
```

## Postup krok za krokem

1. Otevři `database/sample-products.csv` a dodrž přesně hlavičku `active;code;name;description;stock;price`.
2. V `ImportPresenter` nastav maximální velikost 2 MB a povolené typy. Uvědom si, že MIME typ posílá klient, proto není jedinou kontrolou.
3. V `ProductImporter` ověř hlavičku a počet sloupců. Komentář zdůrazní, že chybný řádek se přeskočí s důvodem.
4. Převáděj desetinnou čárku na tečku, ale cenu dál validuj. Kód normalizuj velkými písmeny.
5. Transakce obalí INSERT/UPDATE. Pro každý řádek vyhledej `code`; nalezený kód aktualizuj, jinak vlož.
6. Nahraj `invalid-products.csv`. Ověř, že validní řádek projde a chybné řádky jsou uvedeny ve výsledku.

## Co se právě stalo

UPSERT je rozhodnutí „INSERT, pokud klíč neexistuje; UPDATE, pokud existuje“. V aplikaci je čitelnější napsat toto rozhodnutí explicitně. UNIQUE index `code` zůstává databázovou pojistkou proti závodu nebo chybě.

## Experiment

Nahraj stejný CSV dvakrát. První běh vloží nové kódy, druhý je aktualizuje. Zapiš počty a zkontroluj `updated_at`.

## Miniúkol

Přidej chybu pro duplicitní kód uvnitř jednoho souboru. Rozhodni, zda se má druhý řádek přeskočit, nebo zda je vhodnější zrušit celý import; zdůvodni dopad transakce.

## Minikvíz

1. Co je první fáze importu? **Bezpečně přijmout a zkontrolovat upload.**
2. Co znamená UPSERT? **INSERT nebo UPDATE podle klíče.**
3. Proč výpis chyb? **Aby uživatel věděl, co se nezpracovalo.**
4. Patří CSV parser do presenteru? **Ne, do služby/modelu.**

## Nejčastější chyby

- důvěra v příponu souboru,
- hlavička kontrolovaná až po insertu,
- tiché `continue` bez výsledku,
- parsování v presenteru,
- transakce bez rozmyšlení, co se má při chybě vrátit.

## Kontrolní body

- vzorový CSV vloží i aktualizuje data,
- chybné řádky jsou viditelné,
- importer není v šabloně ani v presenteru jako parser,
- databáze zůstane konzistentní.

## Shrnutí

Upload je jen začátek. Dávkový vstup potřebuje kontrolní fáze a měřitelný výsledek. UPSERT spojuje unikátní kód s rozhodnutím o INSERT/UPDATE.

## Co bude příště

Stejné repository použijeme pro read-only JSON API s odpověďmi 200 a 404.

## Stav projektu po lekci

- Funguje upload, validace, transakční CSV import a přehled výsledku.
- Import neumožňuje měnit uživatele ani spouštět kód z CSV.
- Přibyly `ProductImporter`, vzorový a chybný CSV soubor, Import presenter.

## Poznámka pro učitele

Nechte studenty číst chybný CSV jako diagnostický protokol. Při nedostatku času vynechte detekci duplicit uvnitř souboru, ale nevynechávejte fáze upload–header–validace–výsledek.
