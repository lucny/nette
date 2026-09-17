# Lekce 15 – Bezpečnost a výkon: důkaz místo dojmu

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 14](14-json-api.md) · **Výsledek:** vytvoříte auditní zápis pro konkrétní requesty aplikace, ověříte ochrany na serveru a pojmenujete jejich skutečné limity.

> **🎯 Cíl lekce**
>
> Naučíte se neříkat „aplikace je bezpečná“, ale přesněji: „Tento vstup prochází touto kontrolou, tady je důkaz a toto kontrola nepokrývá.“ Stejný přístup použijete pro výkon seznamu produktů.

## Audit není seznam zkratek

XSS, SQL injection, CSRF a N+1 nejsou magická hesla k odškrtnutí. Každý problém začíná konkrétním vstupem, vede konkrétní cestou k rizikovému místu a vyžaduje ochranu ve správném kontextu.

Pro tento projekt budeme každý nález zapisovat takto:

| Hrozba nebo otázka | Vstup / situace | Ochrana v projektu | Důkaz, který lze udělat | Limit ochrany |
|---|---|---|---|---|
| XSS v názvu | název produktu obsahuje HTML znaky | Latte standardně escapuje textový výstup | vytvořený název se zobrazí jako text, ne jako vykonaný skript | nesmíte bez důvodu použít neescapovaný výstup |
| SQL injection ve filtru | `q` obsahuje SQL znaky | parametry `?` v `where()` | vyhledání neporuší dotaz ani nevypíše jiné řádky | neslepujte uživatelský text do SQL |
| nechtěné mazání přes GET | někdo otevře odkaz v prohlížeči | server vyžaduje POST | GET na delete akci neprovede změnu | potvrzovací dialog v prohlížeči není autorizace |
| velký seznam | v databázi je mnoho produktů | filtr, `count()` a `page()` | Tracy ukáže omezený select pro aktuální stranu | vhodnost indexů musíte měřit nad skutečnými daty |

> **🧠 Nejdřív přemýšlej**
>
> Proč je věta „mám `confirm()`, takže produkt nikdo nesmaže“ chybná? Která část této věty běží v prohlížeči a kterou kontrolu musí zopakovat server?

## Mapa hranic aplikace

```text
prohlížeč / CSV / URL
          │ nedůvěryhodné vstupy
          ▼
Presenter + Nette Form + #[Requires] ──> validátor ──> repository ──> MySQL
          │                                                        │
          └───────────── Latte / JsonResponse <────────────────────┘
                         bezpečný výstup pro daný kontext
```

Tato mapa není důkaz sama o sobě. Ukazuje, kde budeme důkazy hledat: v kódu, v reálné HTTP odpovědi a v diagnostice během lokálního vývoje.

## Postup krok za krokem: 1. XSS – text musí zůstat textem

XSS (*cross-site scripting*) vzniká, když se nedůvěryhodný text vyloží jako kód v prohlížeči. Název produktu je vstup uživatele, tedy nikdy ne „bezpečný HTML text“ jen proto, že jej databáze už uložila.

**📄 Přesná citace z:** `app/Presentation/Product/default.latte`

```latte
<td>{$product->code}</td>
<td>{$product->name}</td>
<td>{$product->stock}</td>
<td>{$product->price}</td>
```

Latte standardně automaticky escapuje proměnné vypsané v HTML kontextu. Znak `<` se proto ve výsledném HTML reprezentuje tak, aby prohlížeč nezaložil tag. Všimněte si slov **v HTML kontextu**: správné escapování závisí na tom, kam hodnotu zapisujete. Text do HTML, hodnota do JavaScriptu a URL nejsou stejný kontext.

### Ověření XSS bez poškození projektu

1. Jako přihlášený uživatel vytvořte testovací produkt s názvem `<script>alert('xss')</script>`.
2. Po uložení ověřte, že se neotevře dialog a že v tabulce vidíte doslovné znaky názvu.
3. Zobrazte zdroj HTML odpovědi a najděte escapovanou podobu značek, například `&lt;script&gt;`.
4. Testovací produkt zase smažte běžným formulářem.

> **⚠️ Pozor**
>
> Neopravujte tento test ručním voláním `htmlspecialchars()` přímo do Latte. Latte už řeší HTML kontext. Dvojí nebo špatně zvolená úprava dat může rozbít legitimní text. Naopak nepřidávejte filtr pro raw/neescapovaný výstup, pokud přesně nevíte, odkud bezpečné HTML pochází a jak bylo očištěno.

## 2. SQL injection: hodnotu předáváme jako parametr

SQL injection vzniká, když program vloží text od uživatele přímo do struktury SQL. Filtr produktů je dobrý test: `q` může obsahovat mezery, apostrof i znaky, které mají v SQL význam.

**📄 Přesná citace z:** `app/Model/Product/ProductRepository.php`, metoda `search()`

```php
$selection = $this->database->table('product')->order('created_at DESC');
$query = trim($query);
if ($query !== '') {
	$like = '%' . $query . '%';
	$selection->where('code LIKE ? OR name LIKE ?', $like, $like);
}
```

`$like` obsahuje hodnotu hledání včetně zástupných znaků `%`. Řetězec SQL ale stále obsahuje jen dvě zástupné značky `?`; Nette Database předá hodnoty jako parametry. To je zásadně jiné než nebezpečný zápis níže:

```php
// Nikdy takto: text z URL se stává součástí syntaxe SQL.
$selection->where("name LIKE '%$query%'");
```

> **📌 Pravidlo pro PHP**
>
> Řetězcová interpolace je běžná vlastnost PHP. Není sama o sobě zakázaná. Je ale špatným nástrojem, když jejím výsledkem má být SQL, příkaz shellu, HTML nebo JavaScript s nedůvěryhodnou hodnotou. Pro SQL použijte parametrizované API databázové vrstvy.

### Ověření filtru

1. Do filtru vložte `Notebook` a poznamenejte si počet výsledků.
2. Potom zkuste text obsahující apostrof, například `A'B`, a nakonec řetězec `' OR 1=1 --`.
3. Stránka se nesmí změnit na seznam všech produktů ani zobrazit surovou SQL chybu. Výsledkem může být nula produktů – to je správně.
4. V Tracy panelu Database se při lokálním debugování podívejte na dotaz a jeho parametry. Nezveřejňujte screenshot s heslem ani konfigurací databáze.

## 3. Mazání je akce POST kontrolovaná na serveru

Odkaz `GET /product/delete/9` lze otevřít náhledovým robotem, uložit do historie nebo omylem načíst. Proto změna dat nesmí záviset na GET. V aplikaci je ochrana ve dvou vrstvách: HTML formulář odešle POST a presenter serverově požaduje POST.

**📄 Přesná citace z:** `app/Presentation/Product/ProductPresenter.php`

```php
#[Nette\Application\Attributes\Requires(methods: 'POST')]
public function handleDelete(int $id): void
{
	if ($this->products->find($id) === null) {
		$this->error('Produkt neexistuje.');
	}

	$this->products->delete($id);
	$this->flashMessage('Produkt byl odstraněn.', 'success');
	$this->redirect('default');
}
```

**📄 Přesná citace z:** `app/Presentation/Product/default.latte`

```latte
<form class="inline-form" method="post" action="{link delete! $product->id}" data-confirm="Opravdu odstranit {$product->name|escapeJs}?">
	<button type="submit">Odstranit</button>
</form>
```

`#[Requires(methods: 'POST')]` je PHP atribut, který Nette vyhodnotí před spuštěním handleru. Jestliže někdo vytvoří GET adresu ručně, k `delete()` se nedostane. `data-confirm` je pouze uživatelský komfort; JavaScript lze vypnout, upravit nebo zcela obejít, proto není bezpečnostní kontrolou.

1. Přihlaste se a vytvořte dočasný produkt.
2. V DevTools → Network zkontrolujte, že stisk tlačítka **Odstranit** odešle `POST`.
3. V prohlížeči nebo nástrojem `curl -i` zkuste stejnou adresu otevřít přes `GET`. Produkt musí zůstat zachován.
4. Teprve potom jej smažte tlačítkem a ověřte přesměrování na seznam.

Tato ukázka řeší metodu a Nette u atributu `Requires` kontroluje u nebezpečných metod také same-origin požadavek podporovanými hlavičkami. Přesto je dobré znát limit: ochranu nelze nahradit pouze klientským dialogem a při návrhu citlivých formulářů se vždy řídíme aktuální dokumentací dané verze Nette.

## 4. Přihlášení, heslo a oprávnění jsou různé věci

**Autentizace** odpovídá na otázku „kdo se přihlašuje?“. **Autorizace** odpovídá na otázku „smí tato identita provést tuto akci?“. Kurzová aplikace vyžaduje přihlášení pro administraci, ale nemá více obchodních rolí ani jemná oprávnění pro jednotlivé produkty.

**📄 Přesná citace z:** `app/Model/User/Authenticator.php`

```php
$row = $this->users->findByEmail($user);
if ($row === null || !$this->passwords->verify($password, (string) $row['password_hash'])) {
	throw new AuthenticationException('Neplatný e-mail nebo heslo.');
}

return new SimpleIdentity((int) $row['id'], ['admin'], ['email' => (string) $row['email']]);
```

`Passwords::verify()` porovnává zadané heslo s uloženým hashem. Aplikace neporovnává dvě otevřená hesla jako běžné řetězce a nikam je neukládá do seed dat ani šablon. Obecná chyba neprozradí, zda existoval e-mail, nebo bylo chybné heslo.

**📄 Přesná citace z:** `app/Presentation/Product/ProductPresenter.php`, metoda `startup()`

```php
if (!$this->getUser()->isLoggedIn()) {
	$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
}
```

To je ochrana cesty k administraci. Neznamená automaticky, že každý přihlášený smí v budoucnu vše – pro více rolí by přibyla konkrétní autorizační pravidla a testy.

## 5. Výkon: změřte, kolik dat a dotazů opravdu teče

„Aplikace je rychlá“ nelze poznat z krátkého testu se třemi produkty. U seznamu chceme aspoň dvě věci: nenačítat všechny řádky do PHP a nevyvolávat dotaz navíc pro každý z nich.

**📄 Přesná citace z:** `app/Model/Product/ProductRepository.php`, metoda `search()`

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

`count()` zjišťuje celkový počet pro navigaci. `page()` vyžádá jen aktuální výsek. `max(1, $page)` je malý příklad PHP funkce, která brání číslu stránky nula a záporným hodnotám. Schéma databáze obsahuje indexy pro hledané a řazené sloupce; jejich přínos se posuzuje nad konkrétním dotazem a objemem dat, ne podle názvu indexu.

### Ověření s Tracy

1. V lokálním vývojovém režimu otevřete `/product?page=1`, pak stránku 2.
2. V Tracy panelu Database najděte dotaz na počet a dotaz na položky stránky.
3. Zapište: kolik SQL dotazů stránka vykonala, jaký má select limit a zda se počet dotazů při přechodu na druhou stranu nemění s počtem zobrazených produktů.
4. Porovnejte se špatnou představou „načtu vše a rozřežu pole v PHP“. Co by se stalo s pamětí a přenosem při desítkách tisíc řádků?

> **⚠️ Produkční hranice**
>
> Tracy je výborný učební a vývojový nástroj. Debug výpis však nesmí v produkci ukazovat stack trace, cesty k souborům, SQL parametry ani konfiguraci. Diagnostika patří do logů a chráněného provozního prostředí, ne do veřejné odpovědi.

## Samostatný úkol: auditní karta

Vyplňte čtyři řádky vlastní auditní karty pro XSS, SQL injection, mazání a upload CSV. U každého napište:

1. konkrétní útočný nebo chybový vstup;
2. přesný soubor/metodu, kde jej aplikace kontroluje;
3. pozorovatelný důkaz;
4. jednu věc, kterou kontrola **neřeší**.

Příklad dobrého limitu: „MIME kontrola uploadu pomáhá formuláři, ale sama nedokazuje, že všechny řádky obsahují správná data.“ Příklad špatného limitu: „Je to asi v pohodě.“

## Minikvíz

1. Proč je `confirm()` bezpečnostně nedostatečný? **Běží u klienta a lze jej obejít; server musí vyžadovat POST a oprávnění.**
2. Co chrání `?` ve `where('… ?', $value)`? **Odděluje hodnotu parametru od syntaxe SQL.**
3. Proč znovu validovat CSV, když formulář ověří velikost? **Velikost neříká nic o hlavičce ani hodnotách řádků.**
4. Je hash hesla totéž co autorizace? **Ne. Hash chrání uložení hesla, autorizace rozhoduje o povolené akci.**
5. Co je N+1 problém? **Jeden dotaz na seznam a další dotaz pro každý jeho řádek.**

## Kontrolní body a ověřené zdroje

- [ ] Testovací `<script>` se zobrazí jako text a nic nespustí.
- [ ] Filtr s SQL znaky neslepí uživatelský vstup do syntaxe SQL.
- [ ] GET nic nesmaže; skutečné tlačítko odešle POST.
- [ ] Umíte pojmenovat rozdíl mezi autentizací, autorizací a hashem hesla.
- [ ] Tracy vám u stránkovaného seznamu poskytl skutečný počet dotazů jako důkaz.

Studujte [Latte a escapování](https://latte.nette.org/en/guide), [Nette Database Explorer](https://doc.nette.org/en/database/explorer), [Nette `#[Requires]` pro změnové requesty](https://doc.nette.org/en/best-practices/post-links), [Nette Passwords](https://doc.nette.org/en/security/passwords), [PHP `htmlspecialchars()`](https://www.php.net/manual/en/function.htmlspecialchars.php) a [MySQL indexy](https://dev.mysql.com/doc/refman/8.4/en/optimization-indexes.html). Odkazy popisují nástroje; audit vždy dělá konkrétní aplikace a její skutečné requesty.

## Stav projektu po lekci

Nevznikla další obrazovka. Vznikl podstatnější výstup: umíte ukázat, jak produktová aplikace chrání vstupy a výstupy, a především kde její kurzový rozsah končí. V poslední lekci tyto důkazy zautomatizujete tam, kde to dává smysl, a sestavíte závěrečný ověřovací scénář.
