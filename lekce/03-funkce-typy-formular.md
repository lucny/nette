# Lekce 03 – Funkce, typy, formulář a HTTP parametry

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 02](02-pole-podminky-cykly.md) · **Nette dnes nepoužíváme.**

> **🎯 Cíl lekce**
>
> URL jako `03-filter.php?q=note` přečte filtr, bezpečně jej předá funkci a vypíše pouze odpovídající produkty. Dokážete odlišit validaci vstupu od escapingu výstupu.

## Dva směry dat

Dosud jsme data psali do souboru. Nyní vstup přichází z prohlížeče. To znamená dvě odlišné otázky:

```text
URL / formulář → Je vstup přijatelný?          = validace
název produktu → Jak jej vložím do HTML?       = escaping
```

Validace neudělá HTML bezpečným a escaping neřekne, zda má dotaz správnou délku nebo tvar. Potřebujeme obojí.

> **🧠 Nejdřív přemýšlej**
>
> Má vyhledávací formulář použít GET, nebo POST? Co se stane s adresou, když filtr pošlete kamarádovi?

## GET, POST a query string

```text
https://test.test/03-filter.php?q=note
                                  └─ query string
```

GET je vhodný pro čtení a filtrování: nic nemění v databázi, URL lze uložit, poslat i obnovit. POST bude patřit změnám dat, například vytvoření produktu. HTTP metoda sama o sobě není kouzelný štít; říká především, jaký druh operace provádíme.

## Soubor a přesný kód filtru

**📄 Výukový fragment z:** `examples/php/03-filter.php`

```php
<?php declare(strict_types=1);

/** @param array<int, array<string, mixed>> $products */
function filterProducts(array $products, ?string $query): array
{
	$query = strtolower(trim((string) $query));
	if ($query === '') {
		return $products;
	}

	return array_values(array_filter($products, static function (array $product) use ($query): bool {
		return str_contains(strtolower((string) $product['code']), $query)
			|| str_contains(strtolower((string) $product['name']), $query);
	}));
}

$query = $_GET['q'] ?? null;
$filtered = filterProducts($products, is_string($query) ? $query : null);
```

Tento blok je fragment z téhož souboru; seznam `$products` a bezpečný výpis najdete v úplném souboru. `??` znamená „použij hodnotu vlevo, pokud existuje a není `null`, jinak použij hodnotu vpravo“.

## Rozbor podpisu funkce

```php
function filterProducts(array $products, ?string $query): array
```

| Část | Význam |
|---|---|
| `function` | začíná definici pojmenovaného opakovatelného úkolu |
| `filterProducts` | jméno, které říká, co funkce dělá |
| `array $products` | první argument musí být pole |
| `?string $query` | druhý argument může být text nebo `null` |
| `: array` | funkce vrátí pole |

Funkce nevypisuje HTML. Má jednu odpovědnost: z předaných produktů vybrat ty, které odpovídají dotazu. Díky tomu ji můžete později vyzkoušet samostatně.

> **⚠️ Pozor**
>
> `(string) $query` není úplná validace. Je to obrana před typem v tomto malém příkladu. V reálném formuláři omezíme délku, případně povolené znaky, a chybovou zprávu ukážeme uživateli.

## Proč `strict_types`

PHP je dynamický jazyk a v běžném režimu někdy převádí typy za nás. `declare(strict_types=1)` na začátku souboru říká: pokud naše funkce slibuje určitý typ, chceme chybu vidět dřív než získat překvapivý výsledek. Nechrání však před nepoctivým HTTP requestem; ten stále musíme validovat.

## Postup krok za krokem

1. Otevřete `examples/php/03-filter.php` a spusťte jej přes Apache s adresou `…/03-filter.php?q=note`.
2. Označte část URL od `?` dál. Je to query string. Změňte `q` na `KB-002` a ověřte, že se hledá ve kódu i názvu.
3. Otevřete adresu bez `?q=…`. Řádek s `?? null` musí zajistit, že se neobjeví warning.
4. Přidejte před výpis jednoduchý HTML formulář:

```html
<form method="get">
	<label>Hledat <input name="q"></label>
	<button>Filtrovat</button>
</form>
```

5. Po odeslání pozorujte adresní řádek. Hodnota se objeví jako `?q=…`; tím formulář nevykonal filtr, jen vytvořil HTTP request. Filtr vykoná až PHP na serveru.
6. Zadejte do `q` text `<b>note</b>`. Vložte hodnotu zpět do inputu pouze přes `htmlspecialchars()`. Pozorujte, že se neprovede jako HTML.
7. Změňte metodu formuláře na `post`, odešlete jej a porovnejte URL. Pak vraťte `get`: filtr je čtecí operace a sdílitelná URL je užitečná.

## Krátká mapa zpracování

```text
GET /03-filter.php?q=note
          ↓
$_GET['q'] ?? null
          ↓ kontrola typu
filterProducts($products, $query)
          ↓
htmlspecialchars() při výpisu
          ↓
HTML odpověď
```

## Experiment: kontrolovaný neplatný vstup

V DevTools nebo ručně v URL zkuste poslat prázdný a velmi dlouhý dotaz. Přidejte pravidlo, že smysluplný dotaz má nejvýše 40 znaků. Pokud je delší, nezobrazujte jej bez upozornění; uložte do proměnné chybovou zprávu a stále bezpečně vypište hodnotu formuláře.

> **💡 Spojení s Nette**
>
> Nette Forms v lekci 10 převezmou rutinu načtení POST, zobrazení chyb a zachování hodnot. Stále ale bude platit, že vstup je nedůvěryhodný a pravidla musí kontrolovat server.

## Samostatný úkol

Rozšiřte `filterProducts()` tak, aby našla text v `code` **nebo** `name`, bez `echo` uvnitř funkce. Přidejte vlastní testovací produkt, jehož název obsahuje český znak. Ověřte vyhledání malými písmeny a napište, proč prosté `strtolower()` není univerzální řešení pro každou abecedu.

## Minikvíz

1. Kde najdeme `q` v URL `/products?q=note`? **V query stringu.**
2. Co znamená `?string`? **Text nebo `null`.**
3. Je `<input type="number">` serverová validace? **Ne.**
4. K čemu je `htmlspecialchars()`? **K bezpečnému vložení textu do HTML.**

## Kontrolní body a zdroje

- [ ] Prázdný parametr `q` nevyvolá warning.
- [ ] Filtr funguje přes URL i přes formulář s metodou GET.
- [ ] Funkce vrací data a sama nevykresluje HTML.
- [ ] Hodnota z formuláře se při opětovném výpisu escapuje.

Čtěte [funkce v PHP](https://www.php.net/manual/en/language.functions.php), [typové deklarace](https://www.php.net/manual/en/language.types.declarations.php), [proměnné z externích zdrojů](https://www.php.net/manual/en/language.variables.external.php) a [MDN: formuláře a GET/POST](https://developer.mozilla.org/en-US/docs/Learn_web_development/Extensions/Forms/Sending_and_retrieving_form_data).

## Stav projektu po lekci

Máme čistý PHP filtr nad polem produktů. Umíme vysvětlit URL, GET, `$_GET`, funkci, parametr, návratovou hodnotu a dvě rozdílné ochrany: validaci a escaping.

**Příště:** místo anonymních asociativních polí vytvoříme skutečný objekt `Product` a zjistíme, jak Composer automaticky načítá třídy.
