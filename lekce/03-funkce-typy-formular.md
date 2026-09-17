# Lekce 03 – Funkce, typy, formulář a HTTP parametry

**Čas:** 2 × 45 minut  
**Výchozí stav:** produkty jsou v PHP poli.

## Co dnes vytvoříme

`examples/php/03-filter.php` přečte bezpečný text z `$_GET['q']`, zavolá typovanou funkci a zobrazí jen odpovídající produkty.

## Co se naučíme

- napsat funkci s parametrem a návratovým typem,
- rozlišit `GET`, `POST`, query string, `$_GET` a `$_POST`,
- vysvětlit `declare(strict_types=1)`, nullable hodnotu a validaci,
- oddělit validaci vstupu od escapingu výstupu.

## Kde jsme skončili

Podmínka a cyklus filtrují pole, ale dotaz je zatím napsaný napevno. Vstup od uživatele je nedůvěryhodný, proto jej přijmeme opatrně.

## Nové pojmy

funkce, parametr, návratová hodnota, type declaration, `string`, `int`, `float`, `bool`, `?string`, GET, POST, query string.

## PHP princip

```php
declare(strict_types=1);

function filterProducts(array $products, ?string $query): array
{
	$query = strtolower(trim((string) $query));
	// Funkce má jeden úkol: vybrat data. Nevykresluje HTML.
	return array_values(array_filter($products, static function (array $product) use ($query): bool {
		return $query === '' || str_contains(strtolower($product['name']), $query);
	}));
}
```

`?string` dovolí řetězec nebo `null`. Převod na `string` řeší pouze očekávaný typ, není to úplná validace. U čísel kontrolujeme rozsah a u textu délku a povolený tvar.

## Nette princip

Čisté PHP zde ukazuje problém, který později řeší Nette Forms: ruční načítání, validaci, zobrazení chyby a zachování hodnoty. Neznamená to, že Nette ruší HTTP; jen poskytne bezpečnější komponentu nad jeho pravidly.

## Jak to funguje

```text
/03-filter.php?q=note
        ↓
$_GET → validace → filterProducts() → escape → HTML
```

GET je vhodný pro hledání, protože URL lze zkopírovat a požadavek nemění data. POST bude patřit formulářům, které vytvářejí nebo mění produkt.

## Postup krok za krokem

1. Spusť `examples/php/03-filter.php?q=note`. Ověř, že query string je část za `?`.
2. Změň `q` na text obsahující HTML. Ověř, že se zobrazí jako text, protože výsledek prochází `htmlspecialchars()`.
3. Odeber `declare(strict_types=1)` a záměrně pošli místo textu pole. Pozoruj rozdíl a vrať deklaraci zpět.
4. Vytvoř ve formuláři textové pole `name="q"` a `method="get"`. Formulář pouze vytvoří URL; filtr stále provede PHP.
5. Přidej kontrolu délky dotazu a vysvětli, že klientská kontrola v HTML nenahrazuje serverovou.

## Co se právě stalo

HTTP přeneslo vstup, PHP jej předalo funkci a výsledek se escapoval až při výpisu. Validace odpovídá na otázku „je vstup přijatelný?“, escaping na otázku „jak ho bezpečně vložím do HTML?“.

## Experiment

Změň metodu formuláře na POST. Sleduj, že `q` zmizí z URL a objeví se v `$_POST`. Napiš, proč by přepnutí na POST zhoršilo sdílení odkazu s filtrem.

## Miniúkol

Přidej filtr podle kódu nebo názvu. Funkce má stále pouze filtrovat data; nesmí obsahovat `echo`.

## Minikvíz

1. Kde je `q` v URL `/products?q=note`? **V query stringu.**
2. Co znamená `?string`? **Řetězec nebo `null`.**
3. Je `<input type="number">` serverová validace? **Ne.**
4. Proč neukládat data z `$_GET` přímo do HTML? **Mohla by obsahovat značky nebo skript.**

## Nejčastější chyby

- přímé `echo $_GET['q']`,
- chybějící výchozí hodnota při neexistujícím parametru,
- funkce s více odpovědnostmi,
- přesvědčení, že POST je automaticky bezpečný,
- nechtěná chyba při `null`.

## Kontrolní body

- filtr funguje z URL i formuláře,
- neznámý nebo prázdný `q` nezpůsobí warning,
- HTML je escapované,
- student vysvětlí rozdíl mezi validací a escapingem.

## Shrnutí

Funkce pojmenovává opakovatelný úkol a typy dokumentují očekávání. GET je vhodný pro čtení a filtry, POST pro změny. Nette později odstraní rutinní část formulářů, ale princip HTTP zůstává stejný.

## Co bude příště

Zabalíme produkt do třídy, ukážeme `private`, konstruktor, namespace a Composer. Potom založíme Nette projekt.

## Stav projektu po lekci

- Funguje čisté PHP filtrování podle kódu a názvu.
- Žádný vstup se nebere jako důvěryhodný jen podle HTML atributu.
- Přibyl `examples/php/03-filter.php`.

## Poznámka pro učitele

Nechte třídu nejdříve předpovědět URL při GET a POST. Na příkladu škodlivého jména ukažte, proč validace a escaping nejsou zaměnitelné. Při nedostatku času vynechte hlubší typové chyby, ale nevynechávejte důvěryhodnost vstupu.
