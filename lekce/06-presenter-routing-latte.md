# Lekce 06 – Presenter, routing a Latte

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 05](05-prvni-aplikace-v-nette.md) · **Výsledek:** stránka produktů přes presenter a Latte.

> **🎯 Cíl lekce**
>
> Dokážete vysvětlit, jak URL `/product` skončí v `ProductPresenter`, proč presenter nepíše SQL do šablony a proč `n:href` odolá změně URL lépe než ručně napsaný odkaz.

## MVP jako rozdělení odpovědností

MVP není sada tajemných složek. Je to dohoda, kam patří který druh práce:

```text
Model        data a pravidla: repository, validátor, importer
Presenter    přijme request, zavolá model, připraví odpověď
View         Latte šablona vytvoří HTML
```

V mnoha materiálech se setkáte s MVC. Pro tento kurz stačí vědět, že Nette používá termín *presenter* pro třídu, která koordinuje stránku. Nezavádíme historickou debatu; soustředíme se na odpovědnost.

> **🧠 Nejdřív přemýšlej**
>
> Kdyby šablona sama poslala SQL dotaz, kdo by ho mohl rozumně otestovat, znovu použít pro JSON API nebo zkontrolovat při chybě?

## Router: URL není název souboru

**📄 Úplný soubor:** `app/Core/RouterFactory.php`

```php
public static function createRouter(): RouteList
{
	$router = new RouteList;
	$router->addRoute('api/products/<code>', 'Api:Products:default');
	$router->addRoute('product[/<action>][/<id>]', 'Product:default');
	$router->addRoute('sign[/<action>]', 'Sign:in');
	$router->addRoute('import[/<action>]', 'Import:default');
	$router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
	return $router;
}
```

Toto je přesný obsah metody z projektu. Úhlové závorky označují parametr, hranaté závorky nepovinnou část masky. Route `product[/<action>][/<id>]` proto rozumí `/product`, `/product/create` i `/product/edit/4`. Pořadí je důležité: konkrétní API route musí být před obecným fallbackem.

## Presenter je PHP třída

**📄 Fragment z finálního souboru:** `app/Presentation/Product/ProductPresenter.php`

```php
final class ProductPresenter extends Nette\Application\UI\Presenter
{
	public function renderDefault(string $q = '', string $status = 'all', int $page = 1): void
	{
		$result = $this->products->search($q, $status, $page);
		$this->template->products = $result['items'];
		$this->template->paginator = $result['paginator'];
	}
}
```

Je to **fragment konečného souboru**; pozdější lekce do něj přidávají formuláře, přihlášení a mazání. Pro tuto lekci si všimněte tří věcí: `extends` dědí chování Nette presenteru, parametry odpovídají URL a `$this->template` předává data pohledu. Presenter nevykresluje `<tr>` a repository nevkládá HTML.

## Latte: šablona není PHP ani databáze

**📄 Fragment:** `app/Presentation/Product/default.latte`

```latte
<tr n:foreach="$products as $product">
	<td><code>{$product->code}</code></td>
	<td>{$product->name}</td>
	<td>{$product->price|number:2,',',' '} Kč</td>
	<td><a n:href="edit $product->id">Upravit</a></td>
</tr>
```

`n:foreach` opakuje HTML element. `{$product->name}` vypisuje proměnnou a Latte ji standardně escapuje. Zápis `|number` je filtr pro formátování. `n:href` vytvoří URL podle routeru; nepíšeme ručně `/product/edit/` a nepřilepujeme k ní neověřený vstup.

> **💡 Co se změnilo od lekce 2**
>
> V PHP poli jsme psali `$product['name']`, protože produkt byl asociativní pole. Nette Database bude vracet objekt řádku `ActiveRow`, proto ve finální Latte šabloně čteme `$product->name`. Zápis určuje typ dat, ne „styl Nette“.

## Postup krok za krokem

1. Otevřete `RouterFactory.php` a v prohlížeči zadejte `/product`. V Tracy Routing panelu ověřte, že router vybral `Product:default`.
2. Zadejte `/product/create`. Nyní může route předat akci `create`; formulář doplníme v lekci 10.
3. Otevřete `ProductPresenter.php` a najděte `renderDefault()`. Vypište si, odkud přijdou tři parametry `q`, `status` a `page`.
4. Otevřete `default.latte`. Najděte každý výskyt `n:href` a zkuste slovně přeložit, na kterou akci vede.
5. Jen v lokálních testovacích datech vložte do názvu text `<b>Test</b>`. Stránka ho musí zobrazit jako text, nikoli jako tučný HTML prvek.
6. Změňte adresu route v kopii projektu a sledujte, že odkazy vytvořené pomocí `n:href` se přizpůsobí. Změnu vraťte zpět.

## Experiment: komu patří odpovědnost?

Pro každý úkol napište jednu volbu a důvod:

| Úkol | Presenter | Latte | Repository |
|---|:---:|:---:|:---:|
| vybrat produkty z databáze |  |  |  |
| vypsat `<td>` s názvem |  |  |  |
| z URL získat stránku |  |  |  |
| rozhodnout SQL `WHERE` |  |  |  |

Řešení: presenter přijímá URL parametr a předá jej dál; repository řeší dotaz; Latte vytváří HTML. Presenter tedy koordinuje, ale nemá v sobě skrývat celý SQL dotaz ani parser CSV.

## Samostatný úkol

Přidejte do Latte sloupec stavu produktu. Pro aktivní položku vypište `aktivní`, jinak `neaktivní`. Použijte podmínku Latte a udržte ji krátkou. Pak napište, proč byste do stejné šablony neměli přidat volání `$database->table('product')`.

## Minikvíz

1. Kdo převádí URL na presenter a akci? **Router.**
2. Je Latte PHP? **Ne, je to šablonovací jazyk.**
3. Kdo tvoří HTML řádky? **Latte view.**
4. Proč `n:href` místo ruční URL? **Router může bezpečně vytvořit aktuální adresu.**

## Kontrolní body a zdroje

- [ ] `/product` vrací stránku produktů.
- [ ] Tracy ukazuje odpovídající presenter a akci.
- [ ] Název s HTML se nevykoná.
- [ ] Odkazy používají `n:href`, ne ručně složené URL.

Použijte [Nette routing](https://doc.nette.org/en/application/routing), [presentery](https://doc.nette.org/en/application/presenters), [šablony](https://doc.nette.org/en/application/templates) a [dokumentaci Latte](https://latte.nette.org/en/guide).

## Stav projektu po lekci

Rozumíme cestě `/product → router → ProductPresenter → default.latte`. Ve finálním projektu už presenter získává skutečná data z repository; v následující lekci nejdřív pochopíme, jak je navrhnout a uložit do MySQL.

**Příště:** vytvoříme databázové schema, které bude chránit jedinečnost produktového kódu i bez prohlížeče.
