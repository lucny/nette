# Lekce 04 – Objekty, třídy a Composer

**Čas:** 2 × 45 minut  
**Výchozí stav:** typované funkce a filtr v čistém PHP.

## Co dnes vytvoříme

V `examples/php/Product.php` vznikne malá třída `Product`. Na konci si Composerem připravíme skutečný Nette web-project.

## Co se naučíme

- vysvětlit třídu, instanci, vlastnost, metodu, konstruktor a `$this`,
- rozebrat `private`, `new`, `->`, `final`, `namespace` a `use`,
- vysvětlit, proč Composer instaluje balíčky a generuje autoloading,
- ověřit `composer.json`, `composer.lock` a `vendor/`.

## Kde jsme skončili

Produkt je asociativní pole. To je praktické pro začátek, ale u větší aplikace chceme pojmenovanou odpovědnost a kontrolu nad tím, co objekt dovolí.

## Nové pojmy

třída, objekt, instance, vlastnost, metoda, konstruktor, viditelnost, `final`, namespace, autoloading, balíček.

## PHP princip

```php
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

$product = new Product('NB-001', 'Notebook');
echo $product->label();
```

`class` je popis, `new` z něj vytvoří objekt a `->` volá jeho metodu. `private` chrání vnitřní stav. `final` říká, že třídu v tomto učebním příkladu nechceme dědit. Namespace zabraňuje kolizím názvů; `use` zkrátí dlouhé jméno.

## Nette princip

Composer není Nette. Je to nástroj PHP ekosystému. Nette je skupina balíčků, které Composer stáhne do `vendor/` a propojí přes `vendor/autoload.php`.

## Jak to funguje

```text
composer.json → Composer → composer.lock + vendor/ → require vendor/autoload.php
                                                     ↓
                                              třídy jsou dostupné
```

## Postup krok za krokem

1. Projdi `examples/php/Product.php` a popiš každý token konstruktoru: typ, viditelnost, vlastnost, `$this`.
2. Vytvoř `new Product('KB-002', 'Klávesnice')` a před spuštěním odhadni výstup.
3. V kořeni projektu spusť `composer validate`. Zkontroluj, že `composer.json` popisuje závislosti a `composer.lock` jejich přesné verze.
4. Otevři `composer.json`. Všimni si PSR-4 mapování `App\` na `app`. Nette 3.3 používá PHP 8.3+, což odpovídá prostředí kurzu.
5. Spusť `composer create-project nette/web-project` jen při zakládání nového projektu. V tomto repozitáři už kostra vznikla z oficiálního web-projectu; proto ji neupravuj přepsáním celého repozitáře.

## Co se právě stalo

Autoloading dovolí napsat `new Product` bez ručního `require` každé třídy. Nette později využije stejný mechanismus pro presentery, repository i služby.

## Experiment

Změň `private` na `public` a vysvětli, jak se mění možnost objekt upravit zvenku. Potom změnu vrať a přidej metodu, která ověřuje validní kód.

## Miniúkol

Přidej `stock` jako typovanou vlastnost a metodu `isLowStock(): bool`. Komentář má vysvětlit hranici 5, ne přepsat název metody.

## Minikvíz

1. Co je instance? **Konkrétní objekt vytvořený z třídy.**
2. Co dělá `->`? **Přistupuje k metodě nebo vlastnosti objektu.**
3. Co je Composer? **Správce PHP závislostí.**
4. Patří `vendor/` do Git repozitáře? **Obvykle ne; vygeneruje se z lock souboru.**

## Nejčastější chyby

- zapomenutý namespace nebo špatné `use`,
- `new` bez povinných argumentů,
- ruční kopírování knihoven místo Composeru,
- úprava `composer.lock` textovým editorem,
- záměna třídy a objektu.

## Kontrolní body

- třída vytvoří čitelný štítek produktu,
- `composer validate` projde,
- `vendor/autoload.php` existuje po instalaci,
- student vysvětlí rozdíl PHP jazyk vs. Composer.

## Shrnutí

Třída spojuje data a chování. Composer řeší instalaci a autoloading. Na tomto základu můžeme přidat Nette, jehož oficiální web-project už obsahuje doporučenou strukturu `app/`, `config/`, `www/` a `vendor/`.

## Co bude příště

Spustíme první vlastní Nette presenter a projdeme tok requestu přes Bootstrap, DI container, router a Latte.

## Stav projektu po lekci

- Funguje objektový příklad produktu a Composer projekt.
- Nette ještě neposkytuje seznam z databáze; máme jen kostru aplikace.
- Přibyl `examples/php/Product.php` a Composer metadata.

## Poznámka pro učitele

Rozložte konstruktor na malé části na tabuli. Studentům nepředávejte větu „tohle je dependency injection“ dříve, než umí vysvětlit parametr a `new`. Při nedostatku času lze vynechat dědičnost, nikoli autoloading.
