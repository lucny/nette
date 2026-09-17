# Lekce 02 – Pole, podmínky, cykly a produkty v paměti

**Čas:** 90 minut · **Navazuje na:** [lekci 01](01-web-a-prvni-php.md) · **Nette dnes nepoužíváme.**

> **🎯 Cíl lekce**
>
> Ze tří produktů v PHP poli vznikne HTML tabulka. Neaktivní produkt se nevypíše a produkt s malým skladem dostane srozumitelné upozornění.

## Proč jedna proměnná pro každý produkt nestačí

V minulé lekci fungovalo `$name`, `$stock` a `$price` pro jediný produkt. Při deseti položkách by vzniklo deset téměř stejných sad proměnných a deset stejných bloků HTML. Pole dovolí data seskupit a cyklus opakující se výpis napsat jen jednou.

```text
produkty v paměti → foreach → if aktivní? → HTML řádek
                                      └── sklad < 5? → upozornění
```

> **🧠 Nejdřív přemýšlej**
>
> Má produkt se skladem `0` zmizet z tabulky, nebo se má zobrazit jako „objednat“? Obě varianty mohou být správně; program potřebuje, abyste pravidlo určili přesně.

## Tři podoby polí

```php
$colors = ['červená', 'modrá'];              // indexované pole: 0, 1, …
$product = ['code' => 'NB-001', 'stock' => 12]; // asociativní pole: vlastní klíče
$products = [$product, ['code' => 'MO-004', 'stock' => 4]]; // vnořené pole
```

Klíč je jméno zásuvky, hodnota je její obsah. U asociativního pole čteme sklad zápisem `$product['stock']`; šipku `->` používáme až pro objekty, které přijdou v lekci 4.

`0`, `false` a `null` nejsou totéž:

| Hodnota | Význam pro sklad / stav |
|---|---|
| `0` | známá číselná hodnota: nic není skladem |
| `false` | logická nepravda: produkt není aktivní |
| `null` | hodnota není k dispozici nebo neexistuje |

## Soubor, se kterým pracujeme

**📄 Výukový fragment z:** `examples/php/02-products.php`

```php
<?php declare(strict_types=1);

$products = [
	['code' => 'NB-001', 'name' => 'Notebook 15', 'active' => true, 'stock' => 12],
	['code' => 'MO-004', 'name' => 'Monitor 24', 'active' => true, 'stock' => 4],
	['code' => 'MS-003', 'name' => 'Myš bez skladu', 'active' => false, 'stock' => 0],
];
?>
<table>
	<tbody>
	<?php foreach ($products as $product): ?>
		<?php if (!$product['active']) { continue; } ?>
		<tr>
			<td><?= htmlspecialchars($product['code'], ENT_QUOTES, 'UTF-8') ?></td>
			<td><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
			<td><?= $product['stock'] < 5 ? 'objednat' : 'v pořádku' ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
```

Tento blok je záměrně zkrácený **fragment**: v úplném souboru najdete také `<thead>` a sloupec skladu. Před kopírováním vždy používejte skutečný soubor v repozitáři.

## Rozbor cyklu po jedné části

```php
foreach ($products as $product) {
```

| Část | Význam |
|---|---|
| `foreach` | opakuj blok pro každý prvek |
| `$products` | pole se všemi produkty |
| `as` | „ulož aktuální prvek jako“ |
| `$product` | proměnná platná pro jeden průchod cyklem |

Uvnitř cyklu rozhodne podmínka:

```php
if (!$product['active']) {
	continue;
}
```

`!` znamená „negace“. Pokud je `active` nepravda, `continue` přeskočí zbytek právě zpracovávaného produktu a pokračuje dalším. Neukončuje celý cyklus.

> **💡 Spojení s Nette**
>
> `foreach` a podmínky jsou PHP principy. V lekci 6 je budeme zapisovat v Latte jako `{foreach}` a `{if}`, aby šablona zůstala přehlednější. Samotné rozhodování podle dat nezmizí.

## Postup krok za krokem

1. Spusťte kopii `02-products.php` přes Apache stejně jako v lekci 1. Nejdřív spočítejte na papír, kolik řádků čekáte: jsou tři produkty, ale jeden má `active => false`.
2. Otevřete zdroj a najděte vnější `[` za `$products =`. Označuje celé pole. Potom najděte vnitřní `[` u každého produktu: to je jedno asociativní pole.
3. Přidejte čtvrtý produkt s vlastním kódem, názvem, aktivitou a skladem. Úmyslně jeden údaj vynechte a sledujte, co se stane při čtení chybějícího klíče; potom jej doplňte.
4. U produktu nastavte `active => false`. Po obnovení se nesmí objevit v tabulce. Vraťte hodnotu na `true`.
5. Nastavte sklad na `4`, `5` a `0`. Zapište, kdy přesně se ukáže text `objednat`.
6. Přidejte pod tabulku `count($products)` a vedle něj vlastní počet skutečně vypsaných řádků. Vysvětlete, proč nejsou vždy stejné.

## Experiment: změna jedné podmínky

Změňte dočasně pravidlo tak, aby se zobrazovaly jen položky **aktivní a skladem**. Nejdřív napište podmínku česky, například „je aktivní a sklad je větší než nula“. Až potom ji převeďte do PHP:

```php
if (!$product['active'] || $product['stock'] <= 0) {
	continue;
}
```

`||` znamená „nebo“. Tento řádek přeskočí produkt, když je neaktivní **nebo** nemá zásobu. Po experimentu obnovte původní pravidlo a do zápisu uveďte, proč se obchodní pravidla mají nejdřív napsat slovy.

## Samostatný úkol

Vytvořte tři stavy skladu v samostatné proměnné `$stockLabel`:

- `Vyprodáno` pro `0`,
- `Nízký sklad` pro `1` až `4`,
- `Skladem` pro `5` a více.

Použijte `if` / `elseif` / `else`. Text potom escapujte jen tehdy, pokud by pocházel z dat; řetězce, které píšete přímo v programu, jsou pod vaší kontrolou, ale stejný návyk je bezpečný a čitelný.

## Minikvíz

1. Co je `['code' => 'NB-001']`? **Asociativní pole.**
2. Co vrátí `count($products)`? **Počet prvků v poli, ne nutně počet vypsaných řádků.**
3. Zastaví `continue` celý cyklus? **Ne, přeskočí aktuální průchod.**
4. Jsou `0` a `null` stejné? **Ne.**

## Kontrolní body, chyby a zdroje

> **🔎 Ověření**
>
> Tabulka obsahuje jen aktivní položky, nízký sklad je rozpoznatelný a při přidání produktu se vytvoří právě jeden nový řádek bez kopírování HTML.

| Chyba | Jak ji poznáte | Oprava |
|---|---|---|
| `$product->name` | PHP hlásí problém s přístupem k hodnotě | pro pole použijte `$product['name']` |
| `=` v podmínce | podmínka mění hodnotu | porovnání vysvětlíme v další lekci; držte se hotového příkladu |
| text bez escapingu | název s HTML se vykreslí jako značka | použijte `htmlspecialchars()` |
| `null` místo skladu | porovnání nedává smysl | nejprve rozhodněte, zda údaj chybí, nebo je nula |

Pokračujte v [kapitole PHP o polích](https://www.php.net/manual/en/language.types.array.php) a [řídicích strukturách](https://www.php.net/manual/en/language.control-structures.php).

## Stav projektu po lekci

Umíme v paměti držet několik produktů, filtrovat je podmínkou a vykreslit cyklem. Data po obnovení souboru stále nevznikají v databázi – zatím je to výhoda, protože se soustředíme jen na PHP pole.

**Příště:** filtr přestane být napsaný napevno; přijmeme jej z URL a rozdělíme práci do pojmenované funkce.
