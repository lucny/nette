# Lekce 01 – Jak funguje web a první PHP

**Čas:** 90 minut · **Navazuje na:** [lekci 0](0-lekce.md) · **Nette dnes nepoužíváme.**

> **🎯 Cíl lekce**
>
> V prohlížeči otevřete PHP stránku s kartou produktu, upravíte její data a podle zdrojového HTML dokážete doložit, že se PHP vykonalo na serveru – prohlížeč zdrojový PHP program nedostal.

## Kde jsme a proč nezačínáme frameworkem

Po lekci 0 běží Apache a PHP. To je přesně dost na první program. Nette si zatím schováme: kdybychom ho přidali hned, slova jako presenter, šablona a router by zakryla základní skutečnost, že webový prohlížeč posílá HTTP požadavek a server vrací odpověď.

```text
prohlížeč ── GET /01-product.php ──► Apache + PHP
prohlížeč ◄── HTML v HTTP odpovědi ── Apache + PHP
```

> **🧠 Nejdřív přemýšlej**
>
> Když do souboru napíšete `$name = 'Notebook';`, uvidí návštěvník stránky znak `$`? Napište odhad dřív, než program otevřete.

## Nové pojmy

| Pojem | Význam v této lekci |
|---|---|
| klient | prohlížeč, který žádá o stránku |
| server | program, který odpoví; u nás Apache s PHP |
| URL | adresa zdroje, například `/01-product.php` |
| request / response | požadavek a odpověď v HTTP |
| proměnná | pojmenované místo pro hodnotu, v PHP začíná `$` |
| escaping | převedení textu tak, aby se v HTML nestal značkou nebo skriptem |

## PHP princip: hodnoty, proměnné a výstup

PHP začíná otvíracím tagem `<?php`. Jednotlivé příkazy zakončuje středník. Proměnná získá hodnotu pomocí `=`; tento znak zde **neporovnává**, ale přiřazuje.

```php
$name = 'Notebook 15'; // string – text
$stock = 12;           // int – celé číslo
$price = 18990.0;      // float – číslo s desetinnou částí
$active = true;        // bool – pravda nebo nepravda
```

Prohlédněte si rozdíl mezi dvěma operátory:

```php
$label = 'Sklad: ' . $stock; // . spojuje texty
$stock = $stock + 1;         // + sčítá čísla
```

> **⚠️ Pozor**
>
> V PHP je `=` přiřazení. Na porovnání se později setkáte s `===`. Nezaměňujte je jen proto, že oba obsahují znak rovná se.

## Soubor, se kterým pracujeme

**📄 Úplný soubor:** `examples/php/01-product.php`

```php
<?php declare(strict_types=1);

// Tato ukázka je čisté PHP: žádný Nette ani databáze.
$code = 'NB-001';
$name = 'Notebook 15';
$price = 18990.0;
$stock = 12;
$active = true;

function e(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="cs">
<head><meta charset="utf-8"><title>Produkt</title></head>
<body>
	<article>
		<h1><?= e($name) ?></h1>
		<p>Kód: <?= e($code) ?></p>
		<p>Cena: <?= number_format($price, 2, ',', ' ') ?> Kč</p>
		<p><?= $active ? 'Aktivní' : 'Neaktivní' ?> · sklad: <?= $stock ?></p>
	</article>
</body>
</html>
```

`declare(strict_types=1)` je instrukce pro PHP: pokud funkce očekává `string`, nechceme, aby se jiný typ tiše převáděl bez našeho rozhodnutí. Funkci `e()` zatím berte jako pojmenovaný pomocník: přijme text, vrátí text bezpečný pro vložení do HTML. Její parametry a návratový typ rozebereme do hloubky v lekci 3.

Zápis `<?= výraz ?>` je zkrácené vypsání hodnoty do právě vznikajícího HTML. Je to obdobné jako `<?php echo výraz; ?>`.

## Proč text escapujeme

Hodnota v proměnné není automaticky bezpečná jen proto, že ji dnes napsal student. Později může přijít z formuláře nebo databáze. Zkuste si představit název produktu:

```text
<strong>Akce</strong>
```

Bez `htmlspecialchars()` by ho prohlížeč mohl vyložit jako HTML značku. S `e($name)` ho zobrazí doslova. To není jen kosmetika; ve chvíli, kdy by někdo vložil `<script>…</script>`, je to základní ochrana před XSS.

> **🔎 Ověření**
>
> Nastavte `$name` na `<strong>Akce</strong>`. Výsledek musí ukázat úhelové závorky jako obyčejný text, ne tučný nápis.

## Postup krok za krokem

1. Otevřete `examples/php/01-product.php` ve VS Code. Nejde o fragment: obsah souboru výše se shoduje s repozitářem.
2. Zkopírujte jej do dočasného projektu z lekce 0, například do `C:\laragon\www\test\01-product.php`. Neotevírejte jej dvojklikem přes `file:///` – tím by PHP nikdo nevykonal.
3. V Laragonu ověřte `Start All`, v prohlížeči otevřete `http://test.test/01-product.php` a poznamenejte si zobrazený název, cenu a sklad.
4. Změňte `$stock` z `12` na `3`, soubor uložte a obnovte stránku. Apache není potřeba restartovat; při novém requestu zpracuje aktuální obsah souboru.
5. Klikněte pravým tlačítkem na stránku a zvolte **Zobrazit zdrojový kód stránky**. Najděte `<h1>Notebook 15</h1>`.
6. Ve zdrojovém HTML hledejte `<?php`, `$name` a `number_format`. Nenajdete je. Poznamenejte si toto jako důkaz rozdílu mezi programem na serveru a odpovědí pro prohlížeč.
7. Vypněte Apache a stránku obnovte. Request selže, protože soubor `.php` není samostatný dokument pro prohlížeč; potřebuje PHP runtime na serveru.

## Jak číst jednu důležitou řádku

```php
<h1><?= e($name) ?></h1>
```

| Část | Co znamená |
|---|---|
| `<h1>` a `</h1>` | HTML značky pro nadpis |
| `<?=` | začátek PHP výpisu hodnoty |
| `e` | naše funkce pro HTML escaping |
| `($name)` | předání hodnoty proměnné jako argumentu funkci |
| `?>` | návrat z PHP do HTML |

> **💡 Spojení s Nette**
>
> V lekci 6 bude stejnou ochranu výstupu běžně provádět Latte. Princip se ale nezmění: data, která vkládáme do HTML, musí být správně escapována pro HTML kontext.

## Experiment: dvě podoby téhož textu

1. Nejprve odhadněte výsledek pro `$name = '<em>Test</em>';`.
2. Ověřte jej s `e($name)`.
3. Jen v kopii souboru nahraďte `e($name)` za `$name`, obnovte stránku a porovnejte výsledek.
4. Změnu vraťte zpět. Do zápisu uveďte, co dokazuje experiment a proč samotné „zákazník to nenapíše“ není bezpečnostní pravidlo.

## Samostatný úkol

Přidejte do karty proměnnou `$description` a vypište ji escapovaně. Dále vytvořte text stavu:

- při skladu `0`: `Vyprodáno`,
- při skladu `1` až `4`: `Nízký sklad`,
- jinak: `Skladem`.

Než řešení napíšete, popište slovně, podle jaké hodnoty program rozhodne. Podmínky `if` procvičíme systematicky v další lekci.

## Minikvíz

1. Co dostane prohlížeč – zdroj PHP, nebo výsledné HTML? **Výsledné HTML.**
2. K čemu slouží znak `$`? **Označuje proměnnou.**
3. Co udělá `.` mezi dvěma texty? **Spojí je.**
4. Proč nepíšeme data přímo do HTML bez escapingu? **Text by se mohl vyložit jako HTML nebo skript.**

## Když to nefunguje

| Příznak | Nejpravděpodobnější příčina | První krok |
|---|---|---|
| prohlížeč stáhne soubor nebo ukáže PHP | soubor není obsloužen Apachem/PHP | zkontrolujte adresu `http://…`, ne `file:///` |
| změna se neukáže | soubor není uložený nebo je otevřená jiná kopie | ověřte celou cestu v záložce editoru |
| `Parse error` | chybí středník, uvozovka nebo závorka | přečtěte první řádek Tracy/PHP chyby |
| zobrazí se nečekané HTML | chybí `e()` | vraťte escaping |

## Kontrolní body a další zdroje

- [ ] Stránka přes Apache vrací kartu produktu.
- [ ] Změna proměnné se projeví po obnovení stránky.
- [ ] Zdroj stránky obsahuje HTML, ale ne `$name` ani `echo`.
- [ ] Text se značkami se nevykoná jako HTML.

Pro další čtení použijte [základní syntaxi PHP](https://www.php.net/manual/en/language.basic-syntax.php), [typy v PHP](https://www.php.net/manual/en/language.types.php) a [funkci `htmlspecialchars`](https://www.php.net/manual/en/function.htmlspecialchars.php).

## Stav projektu po lekci

Funguje jedna karta produktu v čistém PHP. Máme data v proměnných a bezpečný výstup do HTML. Zatím nemáme více produktů, databázi, formulář ani Nette – to je vědomý a spustitelný mezikrok.

**Příště:** data seskupíme do polí a jednu šablonu řádku tabulky necháme vykreslit vícekrát pomocí `foreach`.
