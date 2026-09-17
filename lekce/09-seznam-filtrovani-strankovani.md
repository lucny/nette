# Lekce 09 – Seznam produktů, filtrování a stránkování

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 08](08-database-repository-di.md) · **Výsledek:** databázový seznam s filtrem a stránkami.

> **🎯 Cíl lekce**
>
> Vyhledání podle kódu/názvu a filtrování stavu proběhne v MySQL. Stránka načte jen potřebné řádky a vy dokážete ukázat odpovídající `WHERE`, `COUNT` a omezení v Tracy.

## Problém, který řešíme

Pro osm produktů se může zdát jedno, zda všechny načteme do PHP. Pro sto tisíc už ne. Správné místo pro výběr řádků je databáze, protože tam data leží a indexy jí mohou pomoci.

```text
špatně:  SELECT všechny řádky → PHP pole → filtr → zobrazit 10
správně: q + status + page → SQL WHERE + LIMIT → načíst jen stránku
```

> **🧠 Nejdřív přemýšlej**
>
> Má `/product?q=note&page=2` po obnově ukázat stejnou druhou stránku? Co by se ztratilo, kdyby filtr nešel v URL?

## Parametry přicházejí do presenteru

**📄 Fragment:** `app/Presentation/Product/ProductPresenter.php`

```php
public function renderDefault(string $q = '', string $status = 'all', int $page = 1): void
{
	$result = $this->products->search($q, $status, $page);
	$this->template->products = $result['items'];
	$this->template->paginator = $result['paginator'];
	$this->template->query = $q;
	$this->template->status = $status;
}
```

Je to fragment skutečného souboru. Presenter přijme hodnoty requestu, ale nevytváří SQL. Předá je metodě `search()` a šabloně dá výsledky i původní hodnoty, aby formulář nezapomněl, co uživatel hledal.

## Repository skládá databázový dotaz

**📄 Fragment:** `app/Model/Product/ProductRepository.php`

```php
$selection = $this->database->table('product')->order('created_at DESC');
$query = trim($query);
if ($query !== '') {
	$like = '%' . $query . '%';
	$selection->where('code LIKE ? OR name LIKE ?', $like, $like);
}
if ($status === 'active') {
	$selection->where('active', true);
} elseif ($status === 'inactive') {
	$selection->where('active', false);
}
```

Otazníky `?` nejsou ručně vložené znaky do uživatelova SQL. Jsou to zástupná místa; Explorer předá `$like` jako hodnoty. `%` v `LIKE` znamená libovolný počet znaků před nebo za hledaným textem.

### Stránkování ve dvou krocích

```php
$paginator = new Paginator;
$paginator->setItemCount($selection->count());
$paginator->setItemsPerPage($itemsPerPage);
$paginator->setPage(max(1, $page));

return [
	'items' => $selection->page($paginator->getPage(), $paginator->getItemsPerPage()),
	'paginator' => $paginator,
];
```

Nejprve potřebujeme počet odpovídajících řádků, abychom věděli, kolik stran existuje. Potom `page()` přidá omezení výsledku. Číslo stránky chráníme `max(1, $page)`, aby stránka nula nedávala smysl.

> **⚠️ Omezení tohoto vyhledávání**
>
> `LIKE '%text%'` je pro malý školní katalog čitelný, ale počáteční `%` obvykle nevyužije běžný index stejně jako přesná shoda. Neřešíme to předčasnou složitostí; v lekci 15 se naučíme rozhodnout podle měření a velikosti dat.

## Latte zachovává filtr v odkazu

**📄 Fragment:** `app/Presentation/Product/default.latte`

```latte
<form class="filters" method="get" action="{link default}">
	<label>Hledat <input type="search" name="q" value="{$query}"></label>
	<button type="submit">Filtrovat</button>
</form>

<a n:href="default, q => $query, status => $status, page => $paginator->getPage() + 1">Další →</a>
```

`q => $query` je Latte zápis dvojice jméno–hodnota. Není to asociativní pole PHP napsané ve stejné syntaxi pro zábavu: umožní routeru vytvořit URL s parametry. Hodnota `{$query}` se při výpisu do HTML escapuje.

## Postup krok za krokem

1. Přihlaste se a otevřete `/product`. Poznamenejte si výchozí URL a počet řádků.
2. Vyhledejte `note`. Po odeslání musí URL obsahovat `q=note` a input musí dál ukazovat `note`.
3. Přepněte stav na aktivní a potom neaktivní. Zapište, jak se mění výsledek; zvolte hodnotu `all` a ověřte návrat všech.
4. V repository vyhledejte tři části: `$selection`, `where()` a `Paginator`. U každé napište, zda patří do presenteru, Latte, nebo repository – správně je repository.
5. Přidejte dostatek výukových produktů, aby vznikla druhá stránka. Použijte bezpečné lokální testovací kódy a po pokusu je zase smažte.
6. Klikněte na další stránku. Ověřte, že se `q` a `status` neztratily.
7. V Tracy otevřete Database panel a najděte dotaz s `WHERE` a omezením. Poznamenejte si, že `COUNT` a načtení aktuální stránky mohou být dva smysluplné dotazy.

## Experiment: rozhodnutí před implementací

Máme přidat filtr „prázdný sklad“. Napište odpovědi:

1. Je parametr vhodný pro GET, nebo POST?
2. Kdo ověří povolené hodnoty: šablona, presenter, nebo repository?
3. Kam patří podmínka `stock = 0`?
4. Jak byste v Tracy ověřili, že se nefiltruje až v PHP?

Pak implementujte jen lokální variantu a sledujte dotaz. Nezavádějte index automaticky: nejprve zdůvodněte, podle jakého častého dotazu by se vyplatil.

## Samostatný úkol

Přidejte volbu „nízký sklad“ pro hodnoty `1–4`. Nevkládejte uživatelův text jako název sloupce do `order()` nebo `where()`. Podmínku si stanovte v kódu a jako hodnotu předejte pouze číselné hranice.

## Minikvíz

1. Kde se má provést hledání podle názvu? **V repository/databázi.**
2. K čemu je `LIMIT` nebo `page()`? **Omezí počet načtených řádků stránky.**
3. Znamená `count()` počet PHP objektů na stránce? **Ne, zde počítá odpovídající databázové řádky.**
4. Proč index není automaticky pro každý sloupec? **Stojí místo a zpomaluje zápisy.**

## Kontrolní body a zdroje

- [ ] Filtr i stránka jsou v URL a přežijí navigaci.
- [ ] Databázový panel ukazuje omezený dotaz, ne načtení celé tabulky.
- [ ] Hodnoty se do SQL nepřilepují řetězcem.
- [ ] Tabulka správně vypíše prázdný výsledek.

Čtěte [Nette Database: filtrování a řazení](https://doc.nette.org/en/database/explorer#toc-filtering-and-sorting), [Nette Paginator](https://doc.nette.org/en/utils/paginator) a [MySQL: optimalizace indexy](https://dev.mysql.com/doc/refman/8.4/en/optimization-indexes.html).

## Stav projektu po lekci

Seznam je čtecí GET stránka se sdílitelnými filtry. Repository přenáší výběr do MySQL a Latte pouze zobrazuje aktuální stránku.

**Příště:** začneme data měnit; vytvoříme Nette Form, serverovou validaci a Post/Redirect/Get.
