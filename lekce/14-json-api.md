# Lekce 14 – JSON API: stejná data, jiný klient

**Čas:** 90 minut · **Navazuje na:** [lekci 13](13-csv-import.md) · **Výsledek:** veřejný endpoint `GET /api/products/{code}` vrátí přesně vybraný JSON objekt s HTTP 200, nebo JSON chybu s HTTP 404.

> **🎯 Cíl lekce**
>
> Dokážete odlišit URL, HTTP metodu, stavový kód, hlavičku a tělo odpovědi. Ověříte je v prohlížeči i nástrojem, který ukáže skutečné HTTP hlavičky – ne jen hezky vykreslený JSON.

## Co je API a co rozhodně není

API (*application programming interface*) je zde dohoda mezi serverem a jiným programem. Klient může být mobilní aplikace, JavaScript ve stránce nebo cizí služba. Nečeká na hotový vzhled Latte; potřebuje předvídatelná data.

Endpoint této lekce má tuto smlouvu:

| Část smlouvy | Hodnota | Význam |
|---|---|---|
| metoda | `GET` | pouze čte produkt, nic nevytváří ani nemaže |
| URL | `/api/products/NB-001` | hledá produkt podle kódu `NB-001` |
| úspěch | HTTP `200`, JSON objekt | produkt existuje |
| nenalezeno | HTTP `404`, JSON s `error` a `code` | požadovaný kód neexistuje |
| formát | `Content-Type: application/json` | klient pozná, že tělo je JSON |

```text
GET /api/products/NB-001
          │
          ▼
RouterFactory ──────> Api:Products:default
                              │
                              ▼
                      ProductRepository::findByCode()
                       │                         │
                  produkt existuje           produkt chybí
                       │                         │
                       ▼                         ▼
                JsonResponse + 200       JsonResponse + 404
```

> **🧠 Nejdřív přemýšlej**
>
> Proč nestačí poslat pro neexistující produkt JSON `{"error":"…"}` se stavem 200? Co by musel klient udělat navíc, aby poznal chybu?

## HTML a JSON používají stejné PHP, ale jiný výstup

| HTML produktový seznam | JSON API |
|---|---|
| `ProductPresenter` připraví data pro Latte | `ProductsPresenter` připraví pole pro `JsonResponse` |
| prohlížeč dostane značky, odkazy a CSS | klient dostane hodnoty bez vzhledu |
| uživatel čte stránku | program zpracuje datový kontrakt |
| oba přístupy čtou přes `ProductRepository` | oba přístupy čtou přes `ProductRepository` |

Toto sdílení je podstatné. API si nepíše vlastní SQL dotaz jen proto, že nevrací HTML. Pravidlo „najdi produkt podle kódu“ má jedno místo v repository.

## Kód pod lupou

### 1. Specifická API route je před obecnými routami

**📄 Přesná citace z:** `app/Core/RouterFactory.php`

```php
$router->addRoute('api/products/<code>', 'Api:Products:default');
$router->addRoute('product[/<action>][/<id>]', 'Product:default');
$router->addRoute('sign[/<action>]', 'Sign:in');
$router->addRoute('import[/<action>]', 'Import:default');
$router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
```

Router zkouší pravidla postupně. API route je zapsaná dříve než obecné `<presenter>/<action>[/<id>]`, aby cesta `/api/products/NB-001` neskončila omylem v jiném presenteru. `<code>` je parametr; Nette jej předá metodě `renderDefault(string $code)`.

### 2. Presenter rozhodne mezi „nalezeno“ a „nenalezeno“

**📄 Úplný soubor:** `app/Presentation/Api/ProductsPresenter.php`

```php
<?php declare(strict_types=1);

namespace App\Presentation\Api;

use App\Model\Product\ProductRepository;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;

final class ProductsPresenter extends Presenter
{
	public function __construct(private ProductRepository $products)
	{
		parent::__construct();
	}

	public function renderDefault(string $code): void
	{
		$product = $this->products->findByCode(strtoupper($code));
		if ($product === null) {
			$this->getHttpResponse()->setCode(404);
			$this->sendResponse(new JsonResponse(['error' => 'Produkt nebyl nalezen.', 'code' => $code]));
		}

		$createdAt = $product['created_at'];
		$updatedAt = $product['updated_at'];
		$this->sendResponse(new JsonResponse([
			'code' => (string) $product['code'],
			'name' => (string) $product['name'],
			'description' => (string) $product['description'],
			'stock' => (int) $product['stock'],
			'price' => (float) $product['price'],
			'active' => (bool) $product['active'],
			'createdAt' => $createdAt instanceof \DateTimeInterface ? $createdAt->format(DATE_ATOM) : (string) $createdAt,
			'updatedAt' => $updatedAt instanceof \DateTimeInterface ? $updatedAt->format(DATE_ATOM) : (string) $updatedAt,
		]));
	}
}
```

`strtoupper($code)` sjednotí hledání: klient může zadat `nb-001`, databázový klíč se hledá jako `NB-001`. Podmínka `=== null` používá striktní porovnání; rozlišuje „produkt nebyl nalezen“ od jiných nepravdivých hodnot.

`sendResponse()` přenechá odeslání odpovědi `JsonResponse`. Presenter nevypisuje `echo json_encode(...)` a nepřipojuje Latte layout. `JsonResponse` nastaví JSON odpověď; pole v kódu současně tvoří **whitelist** zveřejněných polí. Není zde `pwdhash`, interní ID uživatele ani celý databázový řádek „pro jistotu“.

### 3. Typy nejsou kosmetika

Všimněte si přetypování:

```php
'stock' => (int) $product['stock'],
'price' => (float) $product['price'],
'active' => (bool) $product['active'],
```

PHP zapisuje `(int)`, `(float)` a `(bool)` jako **type casts**. Bez nich by klient mohl obdržet například text `"15"` místo čísla `15`; výsledek závisí na databázovém ovladači a není dobré jej nechávat náhodě. Čas se formátuje přes `DATE_ATOM`, tedy strojově jednoznačný ISO 8601 zápis včetně časové zóny.

> **⚠️ Pozor: JSON není automaticky veřejný**
>
> Endpoint je v kurzu záměrně read-only a veřejný, aby šel ověřit bez druhého typu přihlášení. V reálném systému byste před zveřejněním posoudili, kdo smí data číst, jaká pole opravdu potřebuje, limity požadavků a logování. „Nikdo data nezmění“ neznamená „nic citlivého neprozradíme“.

## Postup krok za krokem

1. Ověřte, že databáze obsahuje `NB-001` – například v seznamu produktů nebo po importu vzorového CSV.
2. V prohlížeči otevřete `http://127.0.0.1:8088/api/products/NB-001` (nahraďte port či doménu svou lokální adresou). Prohlížeč ukáže JSON, ale zatím nevidíte spolehlivě status ani hlavičky.
3. V terminálu spusťte příkaz; URL upravte podle svého serveru:

   ```bash
   curl -i http://127.0.0.1:8088/api/products/NB-001
   ```

   `-i` vypíše hlavičky před tělem odpovědi. Najděte řádek se stavem `200` a `Content-Type` pro JSON.
4. Zopakujte příkaz pro neexistující kód:

   ```bash
   curl -i http://127.0.0.1:8088/api/products/NO-999
   ```

   Tentokrát očekávejte `404`, ale pořád JSON objekt s klíči `error` a `code`.
5. Otevřete `ProductsPresenter.php` a ukažte na dva výskyty `sendResponse()`. Jeden je větev chyby, druhý větev úspěchu.
6. Otevřete `ProductRepository::findByCode()`. Zapište jednou větou, proč API nepřidává nový SQL dotaz.

> **🔎 Co má být pozorovatelné**
>
> URL s existujícím kódem a URL s chybějícím kódem vrací oba platný JSON. Liší se významem HTTP stavu a obsahem smlouvy, ne tím, že by jedna odpověď byla pěkná stránka a druhá výjimka Tracy.

## Experiment: klient pracuje s kontraktem

Do konzole nástrojů prohlížeče vložte (s vlastní adresou, pokud ji máte jinou):

```js
fetch('/api/products/NB-001')
  .then((response) => {
    console.log('HTTP:', response.status);
    return response.json();
  })
  .then((product) => console.log(product.code, product.price));
```

1. Před spuštěním napište, jaký typ očekáváte u `price` a `active`.
2. Spusťte kód a otevřete rozbalený objekt v konzoli.
3. Změňte kód na `NO-999`. Proč by skutečná aplikace měla kromě `response.json()` zpracovat i `response.ok` nebo `response.status`?

Tento experiment nemění databázi ani aplikaci. Ukazuje ale, proč klient nesmí hádat strukturu odpovědi z HTML stránky.

## Samostatný úkol

Navrhněte pole `stockStatus` s hodnotami `empty`, `low` a `ok`. Napište nejprve tabulku hranic, například `0`, `1–4`, `5 a více`. Potom rozhodněte a zdůvodněte:

- je to údaj uložený v databázi, nebo odvozený z `stock`?
- má jej vytvářet repository, API presenter, nebo samostatná doménová služba?
- jak by se změnil test API kontraktu?

Neimplementujte řešení jen tím, že jej bez vysvětlení doplníte do pole JSON. Podstatný je návrh odpovědnosti a ověřitelná hranice.

## Minikvíz

1. Která metoda endpointu nic nemění? **`GET`.**
2. Co říká HTTP 404? **Požadovaný zdroj nebyl nalezen.**
3. Proč API nepoužívá Latte? **Klient očekává data JSON, ne HTML rozhraní.**
4. Proč přetypovat `stock` na `(int)`? **Aby kontrakt posílal číslo, ne náhodný text z ovladače databáze.**
5. Kde je zapsaná API route? **V `RouterFactory`, před obecnou routou.**

## Kontrolní body a ověřené zdroje

- [ ] `GET` existujícího kódu vrátí JSON a HTTP 200.
- [ ] `GET` neexistujícího kódu vrátí JSON a HTTP 404.
- [ ] Odpověď obsahuje jen výslovně vybraná pole a data jsou správných základních typů.
- [ ] Dokážete nakreslit cestu od route přes repository k `JsonResponse`.

Čtěte [Nette routování](https://doc.nette.org/en/application/routing), [Nette responses](https://doc.nette.org/en/application/responses), referenci [HTTP 404 na MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/404) a [PHP typové deklarace](https://www.php.net/manual/en/language.types.declarations.php).

## Stav projektu po lekci

Aplikace poskytuje jasně vymezené read-only API. HTML a JSON jsou dvě reprezentace dat, které sdílejí repository, ale mají odlišnou odpověď a smlouvu s klientem. V další lekci nebudeme bezmyšlenkovitě přidávat funkci – prozkoumáme hranice, které už aplikace chrání, a jejich limity.
