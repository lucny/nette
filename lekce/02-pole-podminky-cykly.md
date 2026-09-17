# Lekce 02 – Pole, podmínky, cykly a produkty v paměti

**Čas:** 90 minut  
**Výchozí stav:** čistá PHP karta produktu z lekce 01.

## Co dnes vytvoříme

V `examples/php/02-products.php` vypíšeme několik produktů do tabulky, skryjeme neaktivní položky a upozorníme na nízký sklad.

## Co se naučíme

- odlišit indexované, asociativní a vnořené pole,
- použít `if`, `else`, porovnání, `foreach` a `count()`,
- vysvětlit rozdíl mezi `false`, `0` a `null`,
- připravit data tak, aby je později mohl načíst repository.

## Kde jsme skončili

Jedna karta byla zapsaná ručně proměnnými. To se opakuje špatně, proto data seskupíme do pole.

## Nové pojmy

indexované pole, klíč, hodnota, asociativní pole, vnořené pole, podmínka, cyklus, `null`.

## PHP princip

```php
$product = ['code' => 'NB-001', 'stock' => 12];
$products = [$product, ['code' => 'MO-004', 'stock' => 4]];

foreach ($products as $product) {
	if ($product['stock'] < 5) {
		echo 'Objednat'; // Komentář vysvětluje obchodní význam podmínky.
	}
}
```

`$products` je indexované pole, uvnitř jsou asociativní pole. Klíč `stock` vede k hodnotě. `null` znamená „hodnota není k dispozici“, zatímco `0` je skutečná číselná hodnota.

## Nette princip

Stále používáme samotné PHP. Nette později převezme vykreslení tabulky do Latte, ale `foreach` a podmínky nezmizí: jen se přesunou do šablony, kde patří výpis.

## Jak to funguje

```text
pole produktů → foreach → podmínka aktivní → řádek HTML
                         └→ sklad < 5 → upozornění
```

## Postup krok za krokem

1. Otevři `examples/php/02-products.php` a zvýrazni hranaté závorky: jedny vybírají klíč v asociativním poli, druhé vytvářejí seznam.
2. Přidej čtvrtý produkt s `active => false`. Předem odhadni, zda se objeví.
3. Změň podmínku `if (!$product['active']) { continue; }` na variantu, která vykreslí pouze produkty skladem.
4. Přidej řádek s `count($products)` a vysvětli, co měří. Měří počet prvků v paměti, ne počet řádků v budoucí databázi.
5. Vyzkoušej `null` jako popis produktu. Nezaměňuj chybějící hodnotu za prázdný řetězec bez vysvětlení.

## Co se právě stalo

Cyklus opakuje stejný HTML vzor. Podmínka rozhoduje, zda se řádek vytvoří, a data zůstávají oddělená od výpisu. V lekci 9 stejnou myšlenku zachováme, jen filtr proběhne v SQL před načtením řádků.

## Experiment

Před spuštěním odhadni počet řádků po zapnutí filtru „aktivní a sklad větší než 0“. Potom změň jednu hodnotu a ověř, že se změnil právě jeden výsledek.

## Miniúkol

Vypiš zvláštní text `DOPLNIT SKLAD`, pokud je sklad `0`, a `nízký sklad`, pokud je hodnota 1 až 4. Použij `if`/`elseif`/`else`, nikoli tři nezávislé výpisy.

## Minikvíz

1. Co je `['code' => 'NB-001']`? **Asociativní pole.**
2. Co vrátí `count($products)`? **Počet prvků pole.**
3. Je `0` totéž co `null`? **Ne.**
4. Co dělá `continue`? **Přeskočí zbytek aktuálního průchodu cyklem.**

## Nejčastější chyby

- `=` místo `==` nebo `===`,
- použití `$product->name` u pole místo `$product['name']`,
- výpis všech řádků před filtrem,
- předpoklad, že `false`, `0` a `null` znamenají totéž.

## Kontrolní body

- tabulka se generuje cyklem,
- neaktivní produkty se nezobrazí,
- nízký sklad je viditelně označen,
- každý výpis textu je escapovaný.

## Shrnutí

Pole popisují opakující se data, `foreach` je prochází a podmínky rozhodují o výpisu. Tato logika je základ seznamu produktů, ale zatím pracuje jen s malým polem v paměti.

## Co bude příště

Přidáme funkce a formulář, aby filtr reagoval na query string a mohli jsme mluvit o GET a POST.

## Stav projektu po lekci

- Funguje tabulka produktů v paměti s filtrem aktivních a nízkého skladu.
- Data se po obnovení stránky stále načítají z PHP souboru, nikoli z databáze.
- Přibyl `examples/php/02-products.php`.

## Poznámka pro učitele

Nechte studenty napsat podmínku nejdřív slovně. Zastavte se u rozdílu mezi polem a databází. Při nedostatku času vynechte `null` v okrajových variantách, ale nepřeskakujte `foreach` a význam filtru.
