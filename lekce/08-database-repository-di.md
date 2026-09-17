# Lekce 08 – Nette Database, repository a Dependency Injection

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 07](07-mysql-schema.md) · **Výsledek:** produkty čteme z MySQL přes repository.

> **🎯 Cíl lekce**
>
> `ProductRepository` načte produkt podle kódu i seznam z MySQL. Vysvětlíte, proč presenter nevytváří `new Explorer`, a ověříte, že skutečné heslo databáze nezůstalo v Gitu.

## Nejdřív lidsky: co je závislost

`ProductPresenter` chce seznam produktů. Kdyby si uvnitř sám vytvořil databázový objekt, musel by znát DSN, uživatele, heslo a způsob nastavení. To by byla pevná vazba a obtížné testování.

Lepší postup:

```text
1. Presenter řekne: „potřebuji ProductRepository“.
2. Repository řekne: „potřebuji Explorer“.
3. DI container vytvoří správné objekty podle konfigurace.
4. Každý objekt dostane hotovou závislost v konstruktoru.
```

> **🧠 Nejdřív přemýšlej**
>
> Kdyby se zítra změnilo připojení k databázi, v kolika presenterech byste chtěli upravovat heslo? Správná odpověď není „v každém“.

## Konfigurace bez tajemství v repozitáři

**📄 Vzor:** `config/local.neon.example`
**📄 Váš ignorovaný soubor:** `config/local.neon`

Zkopírujte vzor a upravte jen své údaje. `Bootstrap` jej načte pouze tehdy, existuje-li soubor. Necommitujte jej – ověřte to před commitem přes `git status`.

```text
config/common.neon        bezpečné sdílené výchozí nastavení
config/local.neon         skutečná lokální tajemství, ignorováno Gitem
config/services.neon      služby, které může sestavit DI container
```

## Repository: jedno místo pro databázovou práci

**📄 Fragment:** `app/Model/Product/ProductRepository.php`

```php
final class ProductRepository
{
	public function __construct(private Explorer $database)
	{
	}

	public function findByCode(string $code): ?ActiveRow
	{
		return $this->database->table('product')->where('code', $code)->fetch();
	}
}
```

`Explorer` je třída Nette Database. `table('product')` začne sestavovat dotaz, `where('code', $code)` přidá parametrizovanou podmínku a `fetch()` vyžádá jeden řádek nebo `null`.

```text
private Explorer $database
   │       │          │
   │       │          └─ název vlastnosti
   │       └─ datový typ: očekáváme objekt Explorer
   └─ vlastnost mohou používat jen metody repository
```

`$this->database->table('product')` čteme zleva doprava: `$this` je aktuální repository, první `->` vezme jeho vlastnost `database`, druhé `->` volá metodu `table()` na objektu Explorer.

> **⚠️ Pozor na SQL injection**
>
> Správně: `where('code', $code)`. Špatně: vytvořit SQL text typu `"code = '$code'"` spojením řetězců. Parametrizované API předá hodnotu databázi odděleně od struktury dotazu.

## Seznam a lazy selection

**📄 Fragment:** `ProductRepository::search()`

```php
$selection = $this->database->table('product')->order('created_at DESC');
if ($status === 'active') {
	$selection->where('active', true);
}
```

`Selection` představuje dotaz, ne nutně okamžitě načtené všechny řádky. Další metody jej doplňují; na data saháme až při `count()`, iteraci nebo stránkování. To je důležité pro další lekci.

## Postup krok za krokem

1. Vytvořte `config/local.neon` z příkladu. Zkontrolujte DSN, název databáze a připojení s lokálním MySQL.
2. Otevřete `app/Bootstrap.php` a najděte podmínku `if (is_file($localConfig))`. Slovně vysvětlete, proč aplikace funguje i na počítači, kde lokální soubor zatím neexistuje, ale nedostane databázi.
3. Otevřete `services.neon`. Najděte registraci `ProductRepository`; tím container ví, že jde o službu.
4. Ve `ProductRepository.php` najděte importy `use Nette\Database\Explorer;` a `use …ActiveRow;`. `use` zkracuje názvy tříd, nevytváří žádné připojení.
5. Spusťte aplikaci a přihlaste se výukovým účtem. Otevřete `/product`.
6. V Tracy Database panelu najděte dotaz na tabulku `product`. To je důkaz, že data přicházejí z MySQL, ne z pole v PHP.
7. Vyzkoušejte `findByCode('NB-001')` a neexistující `findByCode('NO-999')` například dočasným diagnostickým voláním v bezpečném lokálním testu. Druhý případ musí vrátit `null`, ne výjimku jen proto, že produkt neexistuje.
8. Spusťte `git status --ignored` a ověřte, že `config/local.neon` se nenabízí ke commitu.

## Experiment: proč ne `new` v presenteru

Na papír napište dvě varianty:

```php
// pevná vazba
$repository = new ProductRepository(new Explorer(/* nastavení */));

// závislost předaná konstruktoru
public function __construct(private ProductRepository $products) {}
```

U druhé varianty určete, kdo zná konfiguraci databáze (container), kdo zná dotaz (repository) a kdo rozhoduje o stránce (presenter). Tím odhalíte smysl DI bez slov „magie frameworku“.

## Samostatný úkol

Do repository doplňte a vyzkoušejte přesnou metodu z projektu:

```php
public function find(int $id): ?ActiveRow
{
	return $this->database->table('product')->get($id);
}
```

Ověřte existující i neexistující ID. Proč je návratový typ `?ActiveRow` a ne prostě `ActiveRow`?

## Minikvíz

1. Kam patří lokální heslo? **Do ignorovaného `config/local.neon`.**
2. Co vrátí `fetch()` při nenalezeném kódu? **`null`.**
3. Kdo má znát SQL? **Repository / modelová vrstva.**
4. Co je DI? **Předání potřebného objektu místo skrytého vytváření uvnitř.**

## Kontrolní body a zdroje

- [ ] `/product` zobrazuje řádky z MySQL.
- [ ] Neexistující produkt je uměn reprezentovat `null`.
- [ ] V repository není SQL složené z uživatelského řetězce.
- [ ] `config/local.neon` není mezi verzovanými změnami.

Čtěte [Nette Database Explorer](https://doc.nette.org/en/database/explorer), [připojení a konfiguraci databáze](https://doc.nette.org/en/database/core) a [Nette Dependency Injection](https://doc.nette.org/en/di).

## Stav projektu po lekci

Produkty se čtou z MySQL přes `ProductRepository`; presenter je nemusí hledat ani sestavovat SQL. Připojení zůstává lokální a tajné údaje jsou oddělené od sdílené konfigurace.

**Příště:** stejný dotaz rozšíříme o filtr a stránkování tak, aby databáze neposílala zbytečně všechny produkty do PHP.
