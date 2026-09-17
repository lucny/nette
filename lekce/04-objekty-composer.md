# Lekce 04 – Objekty, třídy a Composer

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 03](03-funkce-typy-formular.md) · **Na konci připravíme Nette projekt.**

> **🎯 Cíl lekce**
>
> Vytvoříte objekt produktu, vysvětlíte rozdíl mezi třídou a instancí, a ověříte, že Composer podle `composer.lock` nainstaloval stejné knihovny a vytvořil autoloader.

## Od pole k objektu

Pole je výborné pro seznam podobných hodnot. Objekt je užitečný, když chceme data a chování pojmenovat společně. Třída je návrh, instance je konkrétní výrobek podle návrhu.

```text
třída Product ── new ──► objekt $product ──► metoda label() ──► text
```

> **🧠 Nejdřív přemýšlej**
>
> Je `Product` samotný notebook, nebo návod, podle kterého lze vytvořit více notebooků? Kdy se objeví konkrétní data `NB-001`?

## Soubor s objektem

**📄 Úplný soubor:** `examples/php/Product.php`

```php
<?php declare(strict_types=1);

namespace Course\Example;

final class Product
{
	public function __construct(
		private string $code,
		private string $name,
	) {
	}

	public function label(): string
	{
		return $this->code . ' – ' . $this->name;
	}
}
```

Tento kód je celý soubor. Neukazuje ještě spuštění s `new Product(…)`; to může být v jiném souboru, který třídu načte.

## Rozebíráme konstruktor bez zkratek

```php
public function __construct(
	private string $code,
	private string $name,
) {
}
```

| Zápis | Co dělá |
|---|---|
| `public` | konstruktor mohou zavolat části programu, které smějí tvořit produkt |
| `function` | začíná metodu |
| `__construct` | speciální metoda spuštěná při `new` |
| `private` | vlastnost zůstává uvnitř objektu |
| `string` | očekávaný typ hodnoty |
| `$code` | název vlastnosti i parametru díky constructor property promotion |

`private string $code` je novější úsporný zápis PHP: vytvoří vlastnost a zároveň přijme hodnotu konstruktoru. Delší, ale významově stejná varianta by měla vlastnost nad konstruktorem a uvnitř přiřazení `$this->code = $code;`.

```php
$product = new Product('NB-001', 'Notebook');
echo $product->label();
```

`new` vytváří konkrétní objekt. `$product->label()` znamená „na objektu v proměnné `$product` zavolej metodu `label`“. `$this` uvnitř metody znamená právě ten objekt, na němž metoda běží.

> **⚠️ Pozor**
>
> `private` není šifrování. Je to pravidlo návrhu programu: okolní kód nemá objektu libovolně měnit vnitřní stav. Přístupové řízení uživatele aplikace řešíme až v lekci 12.

## Namespace a `use`

V různých knihovnách může existovat třída `Product`. Namespace je příjmení třídy: `Course\Example\Product` je jednoznačnější než samotné `Product`. V jiném souboru lze dlouhý název zkrátit:

```php
use Course\Example\Product;

$product = new Product('NB-001', 'Notebook');
```

`use` objekt nevytváří ani nenačítá soubor. Jen dovolí použít kratší jméno. O automatické načtení souboru se stará Composer.

## Composer: problém, který řeší

Bez Composeru by každý soubor ručně psal `require` na všechny třídy a jejich závislosti. Pořadí by se snadno rozbilo. Composer místo toho čte `composer.json`, instaluje balíčky do `vendor/` a vytvoří `vendor/autoload.php`.

```text
composer.json + composer.lock
             ↓ composer install
vendor/ + vendor/autoload.php
             ↓ require
PHP třídy z projektu i knihoven jsou dostupné
```

| Soubor / složka | Úloha |
|---|---|
| `composer.json` | požadavky projektu a nastavení autoloadingu |
| `composer.lock` | přesně vybrané verze pro opakovatelnou instalaci |
| `vendor/` | stažené knihovny a vygenerovaný autoloader |
| `vendor/autoload.php` | soubor, který připojuje `www/index.php` a CLI skripty |

## Postup krok za krokem

1. Otevřete `examples/php/Product.php`. U každého `private` vysvětlete, která hodnota se chrání a proč.
2. Vytvořte malý spouštěcí soubor s `require` a `new Product('KB-002', 'Klávesnice')`. Než jej spustíte, odhadněte výsledek `label()`.
3. V kořeni repozitáře spusťte `composer validate`. Tento příkaz kontroluje formát `composer.json`; nestahuje nové verze.
4. Otevřete `composer.json` a vyhledejte požadavek na PHP a mapování `App\` na adresář `app/`. To je PSR-4 pravidlo pro naši aplikaci.
5. Spusťte `composer install`. Pokud už `composer.lock` existuje, Composer použije jeho přesné verze. Nevyměňuje je automaticky za nejnovější.
6. Ověřte, že existuje `vendor/autoload.php`. Složku `vendor/` ručně neupravujte a necommitujte; znovu vznikne z Composer metadat.
7. Teprve nyní se podívejte na strukturu Nette projektu. V příští lekci nebudeme začínat novým prázdným projektem: tento repozitář už vychází z oficiálního `nette/web-project`.

## Experiment: co skutečně chrání `private`

V kopii třídy změňte `private string $code` na `public string $code`. Ve spouštěcím souboru pak zkuste `$product->code = 'JINÝ-KÓD';`. Uvidíte, že změna je možná. Vraťte `private` a místo přímé změny navrhněte metodu, která by mohla nový kód ověřit.

## Samostatný úkol

Rozšiřte třídu o `private int $stock` a metodu:

```php
public function isLowStock(): bool
{
	return $this->stock < 5;
}
```

Před spuštěním napište dvě hodnoty skladu, pro které čekáte `true` a `false`. Komentář, pokud jej použijete, má vysvětlit obchodní hranici pěti kusů, ne opisovat název metody.

## Minikvíz

1. Co je instance? **Konkrétní objekt vytvořený z třídy.**
2. Co dělá `->`? **Přistupuje k metodě nebo vlastnosti objektu.**
3. Co dělá Composer? **Instaluje závislosti a vytváří autoloading.**
4. Proč je v repozitáři `composer.lock`? **Aby všichni instalovali stejné ověřené verze.**

## Kontrolní body a zdroje

- [ ] Dokážete rozlišit třídu, objekt, vlastnost a metodu.
- [ ] `label()` vrací kód i název pro nový objekt.
- [ ] `composer validate` projde.
- [ ] Po `composer install` existuje `vendor/autoload.php`.

Použijte [PHP: třídy a objekty](https://www.php.net/manual/en/language.oop5.php), [namespaces](https://www.php.net/manual/en/language.namespaces.php), [Composer basic usage](https://getcomposer.org/doc/01-basic-usage.md) a [oficiální instalaci Nette](https://doc.nette.org/en/installation).

## Stav projektu po lekci

Máme pojmenovaný objektový příklad a funkční Composer prostředí. Nette zatím nepoužíváme k datům ani formulářům, ale máme všechny knihovny, které bude potřebovat první webová aplikace.

**Příště:** otevřeme `www/index.php` a krok za krokem projdeme, jak Nette promění request na HTML odpověď.
