# Lekce 08 – Nette Database, repository a Dependency Injection

**Čas:** 2 × 45 minut  
**Výchozí stav:** MySQL má schema a seed.

## Co dnes vytvoříme

`app/Model/Product/ProductRepository.php` načte produkty přes Nette Database Explorer. Připojení bude v `config/local.neon`, který nepatří do Git.

## Co se naučíme

- nastavit DSN, uživatele a lokální heslo,
- použít `Explorer::table()`, `where()`, `order()`, `get()` a `fetch()`,
- vysvětlit repository jako místo odpovědné za data,
- rozebrat constructor property promotion a dependency injection.

## Kde jsme skončili

Databázové tabulky existují, ale presenter ještě neví, jak se k nim bezpečně dostat.

## Nové pojmy

DSN, Explorer, repository, služba, DI container, constructor property promotion, `use`.

## PHP princip

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

`private Explorer $database` je vlastnost vytvořená z parametru konstruktoru. `$this` označuje aktuální repository a `->` volá metody objektu. `?ActiveRow` říká, že produkt nemusí existovat.

## Nette princip

Presenter chce produkty. Repository ví, jak je získat. DI container vytvoří Explorer i repository a předá Explorer do konstruktoru. Heslo zůstává v `config/local.neon`; `config/local.neon` je v `.gitignore`.

## Jak to funguje

```text
ProductPresenter → ProductRepository → Explorer → MySQL
       ↑                  ↑              ↑
       └──────── DI container vytvoří závislosti ────────┘
```

## Postup krok za krokem

1. Zkopíruj `config/local.neon.example` jako `config/local.neon` a uprav pouze lokální údaje.
2. Zkontroluj `config/common.neon`: obsahuje parametry, ne tajný osobní účet.
3. Projdi `ProductRepository`. `where()` parametrizuje hodnotu; nesestavujeme SQL konkatenací vstupu.
4. V `config/services.neon` ověř registraci repository. Třída je služba, nikoli globální proměnná.
5. V presenteru dočasné pole nahraď `$this->products->search('', 'all', 1)`. Nech Tracy ukázat SQL dotaz.
6. Ověř `/product` po spuštění Apache. Při chybě nejdříve zkontroluj DSN, databázi a první řádek Tracy.

## Co se právě stalo

DI není kouzlo: objekt A deklaruje, že potřebuje B; container B vytvoří a předá. Díky tomu presenter nezná heslo, DSN ani konstrukci databázového klienta.

## Experiment

Vytvoř falešnou třídu `MemoryProductRepository` se stejnou metodou. Popiš, proč by šla použít v testu, kdyby presenter závisel na rozhraní místo konkrétní databáze.

## Miniúkol

Přidej metodu `find(int $id): ?ActiveRow`. Použij `get($id)` a otestuj existující i neexistující ID.

## Minikvíz

1. Kam patří lokální heslo? **Do ignorovaného lokálního configu nebo proměnné prostředí.**
2. Co vrátí `fetch()` při neexistujícím kódu? **`null`.**
3. Kdo má znát SQL? **Repository/modelová vrstva.**
4. Co je DI? **Předání potřebných objektů místo jejich skrytého vytváření uvnitř.**

## Nejčastější chyby

- commit `config/local.neon`,
- `new Explorer` v každém presenteru,
- SQL s hodnotou slepenou řetězcem,
- špatný název tabulky nebo namespace,
- připojení k jiné databázi než `nette_products`.

## Kontrolní body

- repository vrací skutečné řádky z MySQL,
- neexistující kód vrací `null`,
- filtr hodnoty se parametrizuje,
- tajný lokální config není v `git status`.

## Shrnutí

Repository izoluje databázi, Explorer zjednodušuje dotazy a DI dodává závislosti. Tím připravujeme půdu pro filtrování a stránkování přímo v SQL.

## Co bude příště

Seznam přestane načítat vše do PHP. Přidáme databázové filtrování, stránkování a indexy.

## Stav projektu po lekci

- Funguje načtení produktů z MySQL přes repository.
- Přihlášení a formuláře ještě nejsou hotové.
- Přibyly `ProductRepository`, konfigurace databáze a lokální šablona configu.

## Poznámka pro učitele

Nakreslete nejprve ruční variantu `new Repository`, potom ji nahraďte předáním v konstruktoru. Při nedostatku času lze vynechat alternativu s memory repository, ale nevynechávejte tajné konfigurace a parametrizaci.
