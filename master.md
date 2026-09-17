# ÚKOL

V tomto repozitáři vytvoř kompletní praktický výukový kurz pro studenty střední školy, kteří zatím nemají dostatečné znalosti PHP ani frameworku Nette.

Kurz musí studenty krok za krokem dovést od úplných základů PHP až k vytvoření funkční databázové webové aplikace v aktuální verzi Nette Frameworku.

Nejde pouze o vytvoření hotové aplikace.

HLAVNÍM VÝSTUPEM JE DIDAKTICKY ZPRACOVANÝ KURZ, ve kterém studenti postupně aplikaci sami vytvářejí a současně chápou:

* PHP,
* objektově orientované programování,
* HTTP,
* HTML formuláře,
* Composer,
* architekturu Nette,
* MVP,
* Presentery,
* Latte,
* Dependency Injection,
* databázi MySQL,
* Nette Database,
* formuláře Nette,
* autentizaci,
* bezpečnost,
* CSV import,
* JSON API,
* základní principy výkonu webové aplikace.

Každý krok musí být vysvětlen. Nepředpokládej předchozí znalost PHP.

---

# 1. VÝCHOZÍ ZADÁNÍ APLIKACE

Student vytvoří jednoduchou administraci produktů.

Produkt obsahuje:

* aktivní/neaktivní,
* kód,
* název,
* popis,
* počet skladem,
* cenu,
* datum vytvoření,
* datum aktualizace.

Aplikace musí obsahovat následující funkce.

## Přihlášení

Přihlášení uživatele pomocí:

* e-mailu,
* hesla.

Uživatelé jsou uloženi v databázi.

Administrace uživatelů není potřeba.

Hesla se nikdy nesmí ukládat v otevřené podobě.

---

## Seznam produktů

Produkty jsou uloženy v MySQL.

Administrace umožní:

* zobrazit produkty,
* stránkovat výsledky,
* filtrovat produkty,
* zobrazit aktivní/neaktivní/všechny,
* hledat podle kódu nebo názvu,
* přejít na editaci,
* odstranit produkt,
* přidat nový produkt.

Filtrování a stránkování prováděj databázově, nikoli načtením všech produktů do PHP.

---

## Přidání a editace

Použij formulář Nette.

Formulář bude obsahovat přinejmenším:

* active,
* code,
* name,
* description,
* stock,
* price.

Vysvětli:

* validaci,
* datové typy,
* zpracování formuláře,
* rozdíl GET a POST,
* Post/Redirect/Get.

---

## Mazání

Mazání nesmí být realizováno nebezpečným GET odkazem typu:

`/product/delete/123`

Použij bezpečnou operaci přes POST.

Před odstraněním zobraz jednoduché potvrzení pomocí JavaScriptu.

V příslušné lekci vysvětli:

* proč GET nemá měnit data,
* HTTP metody,
* same-origin ochranu,
* proč je potvrzení v JS pouze UX prvek a nikoli bezpečnostní mechanismus.

---

## CSV import

Administrace umožní nahrát CSV soubor.

Pokud se v repozitáři nachází vzorový CSV soubor, nejprve jej prozkoumej a dodrž přesně jeho formát.

Pokud vzor chybí, nezastavuj práci a vytvoř dobře zdokumentovaný demonstrační formát.

Import probíhá podle `code`.

Platí:

* existující kód → UPDATE,
* nový kód → INSERT.

Kód produktu musí být unikátní také na úrovni databáze.

Import rozděl na jasné fáze:

1. upload,
2. kontrola souboru,
3. načtení,
4. kontrola hlavičky,
5. parsování,
6. validace řádků,
7. databázový import,
8. výpis výsledku.

Použij databázovou transakci tam, kde je vhodná.

Studentovi vysvětli pojem UPSERT.

Výsledkem importu má být například informace:

* zpracováno,
* vloženo,
* aktualizováno,
* přeskočeno/chybné.

Neignoruj chybná data potichu.

---

## API

Navrhni jednoduchý read-only endpoint:

`GET /api/products/{code}`

Vrací JSON.

Například:

```json
{
    "code": "NB-001",
    "name": "Notebook",
    "description": "15palcový notebook",
    "stock": 12,
    "price": 18990.00,
    "active": true,
    "createdAt": "2026-09-17T10:15:00+02:00",
    "updatedAt": "2026-09-17T12:30:00+02:00"
}
```

Produkt, který neexistuje:

* HTTP 404,
* smysluplná JSON odpověď.

Vysvětli:

* API,
* endpoint,
* HTTP metodu,
* URL,
* status code,
* JSON,
* `Content-Type`,
* serializaci dat,
* rozdíl mezi HTML odpovědí a JSON odpovědí.

Pro výukový projekt může být endpoint veřejný a pouze pro čtení. Explicitně vysvětli, že v reálné aplikaci by se podle požadavků řešila autentizace, autorizace, rate limiting a rozsah zveřejňovaných dat.

---

# 2. TECHNOLOGIE

Používej aktuální stabilní technologie.

Výchozí cílové prostředí:

* PHP 8.3+,
* aktuální stabilní Nette Framework 3.3.x,
* MySQL 8,
* Apache,
* Composer,
* Latte,
* Nette Database,
* Nette Forms,
* Nette Security,
* Tracy,
* HTML5,
* CSS,
* jednoduchý vanilla JavaScript.

Pro vzhled lze použít Bootstrap 5.

NEPŘIDÁVEJ zbytečně:

* React,
* Vue,
* Angular,
* jQuery,
* Doctrine ORM,
* komplikovaný DataGrid,
* komplexní REST framework,
* frontend build systém,
* jiné technologie, které zakrývají principy Nette.

Před zahájením implementace ověř skutečně instalované verze balíčků a jejich současnou oficiální syntaxi.

Nepoužívej zastaralé návody z Nette 2.x nebo starší konvence, pokud nejsou výslovně uváděny jako historické srovnání.

Pokud se aktuální oficiální dokumentace liší od předpokladů tohoto zadání, preferuj aktuální dokumentaci a změnu zaznamenej v kurzu.

---

# 3. HLAVNÍ DIDAKTICKÝ PRINCIP

Studenti NEZNAJÍ PHP.

Proto nevysvětluj například:

```php
final class ProductRepository
{
    public function __construct(
        private Explorer $database,
    ) {
    }
}
```

větou typu:

„Vytvoříme repository a injectneme Explorer.“

To je pro začátečníka nepoužitelné.

Nejdříve rozlož zápis na jednotlivé části:

* co znamená `class`,
* co je objekt,
* proč má třída název,
* co znamená `final`,
* co je metoda,
* co je konstruktor,
* co je parametr,
* co znamená typ před parametrem,
* co znamená `private`,
* co je vlastnost objektu,
* co znamená `$this`,
* co dělá `->`,
* co je `Explorer`,
* odkud se tato třída vzala,
* co znamená `use`,
* proč objekt nevytváříme ručně,
* co je dependency injection.

Teprve potom vysvětli celek.

Používej zásadu:

**nová syntaxe PHP → malý příklad → vysvětlení → použití v Nette → praktický úkol**

---

# 4. V KAŽDÉ LEKCI ROZLIŠUJ PHP A NETTE

Student musí vědět, co poskytuje samotný jazyk a co framework.

Používej pravidelně bloky typu:

## PHP princip

Například:

* pole,
* `foreach`,
* funkce,
* třída,
* konstruktor,
* interface,
* výjimka.

## Co přidává Nette

Například:

* Presenter,
* DI container,
* Latte,
* komponenta,
* Form,
* Explorer.

## Jak spolu souvisejí

Vysvětli, že Presenter je stále PHP třída.

Latte není PHP jazyk.

Nette Form je PHP objekt.

Repository není speciální jazyková konstrukce Nette, ale návrhový způsob organizace PHP kódu.

---

# 5. NEUČ PHP IZOLOVANĚ

Nevytvářej dlouhý samostatný teoretický kurz PHP před Nette.

PHP zaváděj průběžně.

První lekce budou obsahovat PHP výrazně více, pozdější jen nově potřebné konstrukce.

Každá nová konstrukce se má co nejdříve objevit v reálné aplikaci.

Například:

```text
proměnná
→ údaje produktu

pole
→ několik produktů

foreach
→ výpis produktů

funkce
→ formátování ceny

třída
→ objekt produktu / služba

namespace
→ struktura aplikace

constructor
→ závislosti

interface
→ Authenticator

exception
→ chyba přihlášení

try/catch
→ zpracování specifické chyby, pokud je to skutečně vhodné
```

---

# 6. NAVRHOVANÁ POSLOUPNOST KURZU

Vytvoř přibližně 16 navazujících lekcí.

Pokud při implementaci zjistíš, že je pedagogicky vhodnější některou lekci rozdělit, můžeš počet mírně změnit.

Dodrž ale následující logickou posloupnost.

---

## LEKCE 01 – Jak funguje web a první PHP

Cíle:

* klient a server,
* URL,
* HTTP request/response,
* Apache,
* PHP na serveru,
* HTML v prohlížeči.

PHP:

* `<?php`,
* příkaz,
* středník,
* komentáře,
* `echo`,
* proměnná,
* řetězec,
* integer,
* float,
* bool,
* základní operátory.

Prakticky:

Vytvoř jednoduchou PHP stránku produktu bez Nette.

Student například sestaví HTML kartu z PHP proměnných.

Ukazuj zdrojový HTML výstup.

Vysvětli rozdíl mezi:

* PHP zdrojovým kódem,
* tím, co server vykoná,
* HTML, které dostane prohlížeč.

---

## LEKCE 02 – Pole, podmínky, cykly a produkty v paměti

PHP:

* indexované pole,
* asociativní pole,
* vnořené pole,
* `if`,
* `else`,
* porovnávání,
* `foreach`,
* `count()`,
* základní práce s `null`.

Prakticky:

Měj několik produktů zatím pouze v PHP poli.

Vypiš tabulku produktů.

Vyzkoušej:

* pouze aktivní produkty,
* produkty skladem,
* zvýraznění nízkého skladu.

Zařaď několik malých úloh před použitím řešení v aplikaci.

---

## LEKCE 03 – Funkce, typy, formulář a HTTP parametry

PHP:

* deklarace funkce,
* parametry,
* návratová hodnota,
* type declarations,
* `string`,
* `int`,
* `float`,
* `bool`,
* nullable hodnota,
* `declare(strict_types=1)`.

HTTP/PHP:

* GET,
* POST,
* query string,
* `$_GET`,
* `$_POST`.

Prakticky:

V čistém PHP vytvoř jednoduché filtrování produktů.

Vysvětli také:

* proč vstup od uživatele není důvěryhodný,
* validaci,
* escaping HTML.

Toto řešení bude později nahrazeno čistšími prostředky Nette.

---

## LEKCE 04 – Objekty, třídy a Composer

PHP:

* třída,
* instance,
* vlastnost,
* metoda,
* `public`,
* `private`,
* konstruktor,
* `$this`,
* `->`,
* `new`,
* návratový typ,
* `final`.

Potom:

* namespace,
* `use`,
* Composer,
* `composer.json`,
* balíček,
* autoloading,
* `vendor/`.

Vysvětli problém, který Composer řeší.

Teprve na konci lekce založ Nette projekt.

---

## LEKCE 05 – První aplikace v Nette

Vysvětli strukturu projektu.

Především:

* `www/`,
* `app/`,
* `app/Presentation/`,
* `config/`,
* `temp/`,
* `log/`,
* `vendor/`,
* Bootstrap aplikace.

Vysvětli celý tok:

```text
browser
→ Apache
→ index.php
→ Bootstrap
→ DI container
→ Router
→ Presenter
→ Latte
→ HTML response
```

Student musí chápat smysl každého článku, ne implementační detaily všech tříd.

Vytvoř první vlastní Presenter.

---

## LEKCE 06 – Presenter, routing a Latte

Vysvětli MVP:

```text
Model
View
Presenter
```

Porovnej jej stručně s obecně známým MVC, ale nezabíhej do historie frameworků.

PHP:

* dědičnost,
* `extends`,
* `protected`,
* override metody.

Nette:

* Presenter,
* action,
* render metoda,
* template,
* routing,
* generování odkazů.

Latte:

* proměnná,
* `{if}`,
* `{foreach}`,
* odkazy,
* layout,
* escaping.

Přesuň dosavadní seznam produktů do Nette, ještě klidně s dočasnými daty.

---

## LEKCE 07 – Relační databáze a MySQL

Vysvětli:

* databáze,
* tabulka,
* řádek,
* sloupec,
* primární klíč,
* datový typ,
* `NULL`,
* unikátní omezení,
* index.

Navrhni tabulky:

### product

* id,
* active,
* code,
* name,
* description,
* stock,
* price,
* created_at,
* updated_at.

### user

* id,
* email,
* password_hash,
* created_at.

Použij vhodné typy.

`code` musí mít UNIQUE index.

`email` musí mít UNIQUE index.

Cena musí být `DECIMAL`, nikoli `FLOAT`.

Vysvětli proč.

Připrav SQL schema a demonstrační data.

---

## LEKCE 08 – Nette Database a Repository

Připoj MySQL.

Vysvětli:

* connection string,
* konfiguraci,
* proč heslo nepatří do Git repozitáře,
* lokální konfigurační soubor / environment podle zvolené současné konvence projektu.

Nette Database:

* `Explorer`,
* `table()`,
* `where()`,
* `order()`,
* `get()` nebo odpovídající aktuální API.

Vytvoř `ProductRepository`.

Vysvětli pojem repository velmi jednoduše:

„Presenter chce produkty. Repository ví, jak je získat.“

Architektura:

```text
ProductPresenter
      ↓
ProductRepository
      ↓
Nette Database
      ↓
MySQL
```

PHP:

* constructor property promotion,
* dependency injection,
* návratové typy,
* případné namespace importy.

DI vysvětli nejdříve bez magie:

1. objekt A potřebuje objekt B,
2. mohl by si ho vytvořit sám,
3. tím by vznikla pevná vazba,
4. lepší je B objektu A předat,
5. Nette DI container toto předávání automatizuje.

---

## LEKCE 09 – Seznam produktů, filtrování a stránkování

Vytvoř skutečnou administrační tabulku.

Sloupce například:

* stav,
* kód,
* název,
* sklad,
* cena,
* vytvořeno,
* akce.

Přidej:

* hledání podle kódu/názvu,
* aktivní/neaktivní/všechny,
* stránkování,
* řazení pouze pokud zůstane implementace přehledná.

Vysvětli zásadní rozdíl:

ŠPATNĚ:

```text
SELECT všechny produkty
→ PHP
→ filtrovat pole
```

SPRÁVNĚ:

```text
filtr
→ SQL WHERE
→ LIMIT
→ pouze potřebné řádky
```

Použij vhodné indexy.

V Tracy ukaž, jaké SQL dotazy aplikace vytváří.

Vysvětli, proč:

```text
počet produktů v databázi
```

nemá znamenat:

```text
počet objektů načtených do PHP při každém požadavku.
```

---

## LEKCE 10 – Nette Forms a přidání produktu

Nejprve ukaž jednoduchý HTML formulář.

Potom Nette Form.

Student tak pochopí, jaký problém framework řeší.

Vytvoř:

`createComponentProductForm()`

Vysvětli:

* komponentu,
* formulářový control,
* hodnotu,
* validaci,
* required,
* callback,
* `onSuccess`,
* serverovou validaci,
* klientskou validaci.

Vytvoř nový produkt.

PHP:

* callable,
* callback,
* pole callbacků,
* objekt předaný metodě.

Nevysvětluj tyto koncepty pouze terminologií. Použij malé příklady.

---

## LEKCE 11 – Editace, validace a mazání

Znovu použij produktový formulář pro editaci.

Vysvětli:

* INSERT,
* UPDATE,
* načtení počátečních hodnot,
* URL parametr,
* převod a validaci ID,
* 404.

Potom vytvoř bezpečné mazání přes POST.

Použij současné bezpečnostní mechanismy Nette, včetně vhodného omezení HTTP metody / same-origin požadavku.

JavaScript použij pouze pro potvrzení:

```js
confirm(...)
```

Vysvětli:

* proč není `confirm()` bezpečnost,
* proč GET nemá mazat,
* proč nelze důvěřovat údajům z prohlížeče.

---

## LEKCE 12 – Přihlášení a Nette Security

Teprve nyní zaveď autentizaci.

Student už zná:

* formulář,
* databázi,
* repository,
* DI,
* objekt,
* interface.

Vysvětli rozdíl:

* identifikace,
* autentizace,
* autorizace.

Vytvoř:

* `UserRepository`,
* vlastní implementaci `Authenticator`,
* login form,
* logout,
* ochranu administračních Presenterů.

Použij Nette Security a bezpečný password hashing.

PHP:

* interface,
* `implements`,
* kontrakt rozhraní,
* výjimka,
* případně enum/konstanty pouze pokud mají skutečný didaktický přínos.

Vysvětli hesla:

```text
heslo
→ password hash
→ databáze
```

nikoli:

```text
heslo
→ databáze
```

Uživatelský účet pro výuku vytvoř bezpečným CLI postupem nebo jiným vhodným jednorázovým nástrojem mimo veřejný `www/`.

Nevytvářej veřejnou stránku „create admin“.

---

## LEKCE 13 – CSV import

Vytvoř samostatnou službu:

`ProductImporter`

Nedávej parsování CSV do Presenteru.

Architektura:

```text
ImportPresenter
       ↓
ProductImporter
       ↓
ProductRepository / Database
       ↓
MySQL
```

PHP:

* práce se souborem,
* stream,
* cyklus,
* parsování CSV,
* výjimky/chybové stavy,
* datová transformace.

Nette:

* file upload,
* validace uploadu.

Vysvětli databázovou transakci.

Vysvětli UPSERT.

Zdůrazni unikátní index `code`.

Zahrň chybný CSV soubor jako výukový příklad.

Student má zjistit, co je špatně.

---

## LEKCE 14 – JSON API

Vytvoř:

`GET /api/products/{code}`

Použij stejný `ProductRepository`.

Vysvětli důležitou architektonickou myšlenku:

```text
                   ┌→ Presenter → Latte → HTML
ProductRepository ─┤
                   └→ API Presenter → JSON
```

Modelová vrstva se nemusí duplikovat jen proto, že výstup má jiný formát.

Procvič:

* status 200,
* status 404,
* JSON,
* hlavičku Content-Type.

Otestuj endpoint také pomocí:

* prohlížeče,
* `curl`,
* případně jednoduchého JS `fetch()` jako nepovinné rozšíření.

---

## LEKCE 15 – Bezpečnost a výkon celé aplikace

Systematicky projdi projekt a vysvětli:

* XSS,
* escaping Latte,
* SQL injection,
* parametrizované databázové dotazy,
* CSRF / same-origin principy,
* bezpečné zpracování formulářů,
* hesla,
* session,
* autentizaci,
* bezpečnost uploadu,
* validaci CSV,
* veřejný adresář `www`,
* produkční versus vývojové prostředí.

Výkon:

* indexy,
* stránkování,
* databázové filtrování,
* `LIMIT`,
* zbytečné dotazy,
* N+1 problém pouze pokud se v projektu relevantně projeví,
* využití Tracy pro diagnostiku.

Ukaž konkrétní chybnou variantu a její opravu.

---

## LEKCE 16 – Refactoring, testování a závěrečná výzva

Prohlédni celý projekt.

Student musí být schopen vysvětlit:

```text
HTTP request
→ Router
→ Presenter
→ Model
→ Database

Presenter
→ Latte
→ HTML response
```

a pro API:

```text
HTTP request
→ Router
→ Presenter
→ Model
→ JSON response
```

Proveď rozumný refactoring.

Zaveď několik jednoduchých automatických testů tam, kde mají didaktický význam.

Minimálně otestuj vhodným způsobem:

* importní logiku,
* vyhledání produktu podle kódu,
* některé validační chování,
* API 200/404, pokud zvolená testovací infrastruktura umožňuje rozumně jednoduché řešení.

Na závěr připrav několik samostatných studentských rozšíření, například:

* kategorie produktů,
* řazení,
* minimální sklad,
* API seznam produktů,
* změna hesla,
* export CSV.

Nedodávej okamžitě celé řešení závěrečných úloh.

---

# 7. FORMÁT KAŽDÉ LEKCE

Každá lekce musí být samostatný Markdown dokument.

Použij například:

```text
course/
├── README.md
├── COURSE-MAP.md
├── 01-web-a-prvni-php.md
├── 02-pole-podminky-cykly.md
├── ...
└── 16-zaverecna-lekce.md
```

Každá lekce MUSÍ obsahovat následující části.

## 1. Co dnes vytvoříme

Stručně ukaž praktický výsledek.

## 2. Co se naučíme

Konkrétní learning outcomes.

Nepoužívej vágní:

„Student se seznámí s databází.“

Raději:

„Student dokáže vysvětlit rozdíl mezi primárním klíčem a unikátním indexem.“

## 3. Kde jsme skončili

Shrň stav projektu z minulé lekce.

## 4. Nové pojmy

Krátký přehled.

## 5. PHP princip

Podrobně vysvětli novou PHP syntaxi.

## 6. Nette princip

Pokud se v lekci používá nový mechanismus frameworku.

## 7. Jak to funguje

Použij jednoduchý diagram.

Preferuj Mermaid nebo textový diagram podle toho, co bude spolehlivě zobrazitelné v cílovém Markdown prostředí.

## 8. Postup krok za krokem

Toto je hlavní část.

Každý krok musí říkat:

1. co děláme,
2. v jakém souboru,
3. jaký kód píšeme,
4. co jednotlivé části znamenají,
5. proč to děláme,
6. jak ověříme výsledek.

## 9. Co se právě stalo

Po významnější části zastav výklad a slovně popiš tok aplikace.

## 10. Experiment

Například:

„Změň podmínku a zjisti, co se stane.“

Student se nemá omezit na opisování.

## 11. Miniúkol

Malá samostatná změna.

## 12. Minikvíz

3–6 otázek.

Použij různé typy:

* výběr,
* pravda/nepravda,
* předpověď výstupu,
* nalezení chyby,
* vysvětlení kódu.

## 13. Nejčastější chyby

Ukaž skutečné chyby začátečníků.

Například:

* chybějící `;`,
* záměna `=` a `==`,
* chybějící `$`,
* špatný namespace,
* špatný typ,
* chybějící služba v DI,
* neexistující template,
* špatná route.

## 14. Kontrolní body

Student si ověří funkčnost.

Například:

* `/products` vrací HTTP 200,
* zobrazí se 20 produktů,
* filtr zachová hodnotu po odeslání,
* nevyskytuje se PHP warning.

## 15. Shrnutí

Maximálně několik nejdůležitějších bodů.

## 16. Co bude příště

Krátké propojení s další lekcí.

---

# 8. PRÁCE S KÓDEM

Kód musí být didakticky kvalitní.

U každého významného bloku uveď cestu k souboru.

Například:

```text
app/Model/Product/ProductRepository.php
```

Nedávej studentovi blok:

```php
// ...
// zbytek kódu
// ...
```

pokud má být kód zkopírován a spuštěn.

Rozlišuj:

* úplný obsah souboru,
* změněnou část souboru,
* demonstrační fragment.

U fragmentu jasně řekni, že nejde o celý soubor.

Kód z kurzu a skutečný kód projektu musí být synchronizované.

Nikdy nesmí nastat situace, kdy dokument učí jinou implementaci, než obsahuje repozitář.

---

# 9. STYL VÝKLADU

Piš česky.

Používej normální populárně-naučný technický styl.

Nevytvářej jen stručné body.

Student musí dostat skutečné vysvětlení.

Nevhodné:

„Dependency Injection slouží k injektování závislostí.“

Vhodnější:

„`ProductPresenter` potřebuje objekt, který umí pracovat s produkty. Mohl by si uvnitř vytvořit `ProductRepository` pomocí `new`, tím by ale pevně rozhodoval také o způsobu jeho vytvoření. Místo toho deklaruje, že tento objekt potřebuje, a Nette DI container mu připravenou instanci předá.“

Terminologii používej přesně, ale vždy ji vysvětli.

Nepoužívej nevysvětlený jargon.

---

# 10. KOMENTÁŘE V KÓDU

Komentáře používej rozumně.

Nevytvářej:

```php
$id = 10; // nastavíme id na 10
```

Komentář má vysvětlovat důvod nebo neobvyklé chování.

Hlavní vysvětlení patří do výukového textu, ne do stovek komentářů ve zdrojovém kódu.

---

# 11. ARCHITEKTURA APLIKACE

Cílová struktura může být přibližně:

```text
app/
├── Model/
│   ├── Product/
│   │   ├── ProductRepository.php
│   │   └── ProductImporter.php
│   └── User/
│       ├── UserRepository.php
│       └── Authenticator.php
│
├── Presentation/
│   ├── Product/
│   │   ├── ProductPresenter.php
│   │   └── ...
│   ├── Import/
│   ├── Sign/
│   └── Api/
│
├── Core/
└── Bootstrap.php
```

Přizpůsob ji aktuální doporučené struktuře Nette 3.3.

Nevytvářej vrstvy jen proto, aby projekt vypadal „enterprise“.

Pro tento kurz stačí jasně oddělit:

* presentation,
* aplikační/modelovou logiku,
* databázovou práci.

---

# 12. MVP A ODPOVĚDNOSTI

Důsledně zabraň „fat presenterům“.

Presenter má především:

* přijmout požadavek,
* získat parametry,
* zavolat vhodnou službu/repository,
* vybrat odpověď,
* předat data šabloně.

Presenter nemá obsahovat:

* desítky řádků CSV parseru,
* ručně sestavenou autentizaci,
* komplikovanou databázovou logiku.

Latte nesmí provádět business logiku nebo databázové dotazy.

---

# 13. BEZPEČNOST

Bezpečnost neodkládej pouze do lekce 15.

Když se objeví nový mechanismus, vysvětli bezpečnost rovnou.

Například:

HTML výstup:
→ XSS a escaping.

SQL:
→ SQL injection.

Form:
→ nedůvěryhodný vstup, validace.

Delete:
→ HTTP metoda a same origin.

Login:
→ password hashing a session.

Upload:
→ velikost, typ, stav uploadu.

CSV:
→ neplatná data.

Konfigurace:
→ hesla mimo Git.

Produkce:
→ debugger nesmí zveřejňovat citlivé informace.

---

# 14. VÝKON

Nevytvářej umělou mikrooptimalizaci.

Zaměř se na architektonicky významné věci:

* nenahrávat všechny produkty,
* filtrovat databázově,
* stránkovat,
* indexovat `code`,
* vhodně indexovat používané filtry,
* nepřidávat zbytečné SQL dotazy v cyklu,
* analyzovat dotazy pomocí Tracy.

Pokud nějaká optimalizace přidává velkou složitost a nemá pro tento projekt význam, pouze ji vysvětli jako pokročilé téma.

---

# 15. DATABÁZOVÉ SCHÉMA

Připrav:

```text
database/
├── schema.sql
└── seed.sql
```

Případně použij jednoduchý migrační mechanismus, pouze pokud nezvýší zbytečně komplexitu kurzu.

Pro začátečníky preferuj transparentnost.

Seed data mají obsahovat dost produktů, aby:

* stránkování mělo smysl,
* šla demonstrovat filtrace,
* existovaly aktivní i neaktivní položky,
* některé produkty nebyly skladem.

Nevkládej produkční heslo do repozitáře.

---

# 16. CSS A UI

Použij jednoduché, profesionální administrační rozhraní.

Bootstrap 5 je vhodný.

UI nesmí být cílem kurzu.

Použij například:

* navbar,
* container,
* formuláře,
* table,
* badge,
* alert,
* pagination,
* buttons.

Nevytvářej složitý frontend.

Student musí v HTML stále poznat, jak stránka funguje.

---

# 17. JAVASCRIPT

Používej vanilla JavaScript a pouze tam, kde má smysl.

Povinné použití:

* potvrzení odstranění produktu.

Nepřenášej logiku aplikace do JavaScriptu.

Volitelné rozšíření:

* jednoduchý `fetch()` proti vlastnímu API v posledních lekcích.

Vysvětli, že klientská validace ani JavaScript nemohou nahradit serverovou kontrolu.

---

# 18. PRŮBĚŽNÉ PHP MINILEKCE

V každém dokumentu zkontroluj, zda se neobjevuje dosud nevysvětlená syntaxe.

Pokud ano, přidej krátké vysvětlení.

Zvlášť hlídej:

* `$`,
* `->`,
* `::`,
* `=>`,
* `[]`,
* `?`,
* `??`,
* `===`,
* namespace,
* `use`,
* `new`,
* `extends`,
* `implements`,
* visibility,
* constructor,
* type declarations,
* return type,
* nullable type,
* closures/callbacky,
* attributes,
* exceptions.

Nepředpokládej, že student význam symbolu odvodí.

---

# 19. CVIČENÍ

Každá lekce má obsahovat nejméně:

* jeden malý experiment,
* jeden samostatný úkol,
* jeden minikvíz.

První PHP lekce mohou mít více krátkých úloh.

Příklady vhodných úloh:

„Před spuštěním odhadni výstup.“

„Najdi v kódu chybu.“

„Uprav podmínku tak, aby…“

„Přidej další filtr.“

„Vysvětli vlastními slovy, proč…“

„Která vrstva má tuto operaci provádět?“

---

# 20. NEUČ STUDENTA POUZE OPISOVAT

Pravidelně používej fázi:

## Nejdřív přemýšlej

Polož otázku ještě před zobrazením řešení.

Například:

„Má hledání podle názvu probíhat až po načtení všech produktů do PHP, nebo už v databázi? Jaký bude rozdíl při 100 000 produktech?“

Následuje vysvětlení a implementace.

---

# 21. DIAGRAMY

Používej diagramy všude, kde pomohou vysvětlit tok.

Klíčový diagram postupně rozšiřuj:

```text
Browser
   ↓
Router
   ↓
Presenter
   ↓
ProductRepository
   ↓
Nette Database
   ↓
MySQL
```

a:

```text
Presenter
   ↓
Latte
   ↓
HTML
   ↓
Browser
```

Pro API:

```text
Browser / client
      ↓
     HTTP
      ↓
ApiPresenter
      ↓
ProductRepository
      ↓
MySQL
      ↓
JSON response
```

---

# 22. STAV PROJEKTU PO KAŽDÉ LEKCI

Projekt musí po každé lekci fungovat.

Nevytvářej mezistav, který několik lekcí nelze spustit.

Na konci každého dokumentu přidej:

## Stav projektu po lekci

Uveď:

* co již funguje,
* co ještě záměrně nefunguje,
* které hlavní soubory přibyly nebo se změnily.

Pokud je repozitář Git a nejsou tím ohroženy existující změny, vytvářej logické commity například:

```text
lesson-01: first PHP page
lesson-02: product arrays and loops
...
lesson-16: final review and tests
```

Nikdy nepřepisuj nebo nemaž cizí nerozpoznané změny jen kvůli vytvoření commitu.

---

# 23. COURSE-MAP

Vytvoř `course/COURSE-MAP.md`.

Musí obsahovat:

* pořadí všech lekcí,
* stručný obsah,
* předpoklady,
* výsledný stav projektu,
* funkční relativní odkazy na všechny soubory.

Odkazy skutečně ověř.

Nesmí vzniknout course map s odkazy na neexistující soubory.

---

# 24. README KURZU

`course/README.md` musí vysvětlit:

* pro koho kurz je,
* co student vytvoří,
* požadované prostředí,
* jak projekt nainstalovat,
* Composer,
* PHP,
* Apache,
* MySQL,
* konfiguraci databáze,
* inicializaci schema,
* seed data,
* spuštění,
* testování,
* doporučené pořadí lekcí.

Odděl instrukce pro:

* studenta,
* učitele.

---

# 25. UČITELSKÉ POZNÁMKY

Pro každou lekci vytvoř stručnou sekci:

## Poznámka pro učitele

Obsahuje:

* co bývá pro studenty nejtěžší,
* kde je vhodné se zastavit,
* jakou otázku položit třídě,
* co lze při nedostatku času vynechat,
* co naopak nesmí být přeskočeno.

---

# 26. ČASOVÁ NÁROČNOST

U každé lekce odhadni rozsah například:

* 45 min,
* 60 min,
* 90 min,
* 2 × 45 min.

Nepřizpůsobuj obsah násilně délce.

Pokud je lekce příliš rozsáhlá, rozděl ji.

Kurz má být pochopitelný, ne pouze rychlý.

---

# 27. OVĚŘOVÁNÍ FUNKČNOSTI

Po implementaci každého většího kroku projekt skutečně zkontroluj.

Podle dostupného prostředí použij:

* Composer validation,
* PHP syntax check,
* testy,
* spuštění aplikace,
* HTTP požadavky,
* databázové dotazy.

Nepovažuj „kód vypadá správně“ za dostatečné ověření.

Pokud něco nelze v prostředí spustit, napiš konkrétně co a proč.

---

# 28. KVALITA KÓDU

Používej:

```php
declare(strict_types=1);
```

tam, kde to odpovídá současné struktuře projektu.

Používej:

* typované parametry,
* návratové typy,
* smysluplné názvy,
* krátké metody,
* dependency injection,
* oddělení odpovědností.

Nevytvářej zbytečné abstrakce.

Každá abstrakce musí mít vysvětlitelný účel.

---

# 29. ČEMU SE VYHNOUT

Nevytvářej kurz jako řadu příkazů:

„Vytvoř tento soubor.“
„Vlož tento kód.“
„Spusť.“

bez vysvětlení.

Nevytvářej lekce tvořené převážně odrážkami.

Nevysvětluj PHP konstrukci až několik lekcí po jejím prvním použití.

Nepoužívej nekompletní kód jako údajně hotové řešení.

Nevytvářej „magické“ utility, které student neumí vysvětlit.

Nevkládej databázovou logiku do Latte.

Nevkládej CSV parser do Presenteru.

Neukládej plaintext hesla.

Nemaž pomocí GET.

Nefiltruj desetitisíce produktů až v PHP.

Nenačítej celou tabulku jen kvůli stránkování.

Nevěř hodnotám z formuláře jen proto, že HTML obsahuje například:

```html
<input type="number">
```

Server musí data ověřit.

---

# 30. DIDAKTICKÉ ZNAČENÍ KÓDU

Při prvním použití složitějšího zápisu jej rozeber.

Například:

```php
private ProductRepository $products;
```

vysvětli:

```text
private
│
│  ProductRepository
│  │
│  │                 $products
│  │                 │
▼  ▼                 ▼
viditelnost   datový typ   název vlastnosti
```

Podobně:

```php
$this->products->findByCode($code);
```

student musí vědět:

* `$this` = aktuální objekt,
* `->` = přístup k vlastnosti nebo metodě objektu,
* `products` = vlastnost,
* druhé `->` = volání metody repository,
* `findByCode` = metoda,
* `$code` = argument.

Takové rozbory používej v prvních lekcích často a postupně jejich četnost snižuj.

---

# 31. KONTROLNÍ ARCHITEKTONICKÉ OTÁZKY

V průběhu kurzu se opakovaně ptej:

„Kdo má tuto odpovědnost?“

Například:

Kdo načte produkt z databáze?

* Latte?
* Presenter?
* ProductRepository?

Kdo rozhodne, kterou stránku obslouží požadavek?

* Router.

Kdo vytvoří HTML?

* Latte.

Kdo zpracuje business logiku importu?

* ProductImporter.

Kdo předá ProductRepository Presenteru?

* DI container.

Tím průběžně upevňuj architekturu.

---

# 32. FINÁLNÍ AKCEPTAČNÍ KRITÉRIA APLIKACE

Na konci musí fungovat:

### Authentication

* login e-mailem a heslem,
* logout,
* zabezpečená administrace,
* bezpečné hashování hesla.

### Products

* seznam,
* filtrování,
* stránkování,
* vytvoření,
* editace,
* odstranění s potvrzením.

### CSV

* upload,
* validace,
* insert nových kódů,
* update existujících kódů,
* přehled výsledku.

### API

* získání produktu podle code,
* JSON,
* správné HTTP statusy.

### Architecture

* presentation oddělena od modelové/databázové logiky,
* žádné databázové dotazy v Latte,
* minimální logika v Presenterech.

### Security

* žádná plaintext hesla,
* žádné SQL skládáním nedůvěryhodných hodnot,
* bezpečné HTML,
* validované formuláře,
* bezpečné mazání,
* bezpečný upload.

### Performance

* server-side filtering,
* pagination,
* index nad product.code,
* žádné načtení celé tabulky při běžném seznamu.

---

# 33. FINÁLNÍ AKCEPTAČNÍ KRITÉRIA KURZU

Kurz není hotový jen proto, že funguje aplikace.

Kurz je hotový až tehdy, když:

* všechny lekce existují,
* mají správné pořadí,
* odkazy fungují,
* každá lekce vychází z předchozího stavu,
* každá nová významná PHP konstrukce je vysvětlena,
* každá významná Nette konstrukce je vysvětlena,
* kód v dokumentaci odpovídá projektu,
* každý kopírovatelný příklad je úplný,
* každá lekce obsahuje praktické ověření,
* jsou přítomny experimenty,
* jsou přítomny samostatné úkoly,
* jsou přítomny minikvízy,
* bezpečnost je vysvětlována průběžně,
* aplikaci lze podle kurzu skutečně vytvořit od začátku.

---

# 34. POSTUP PRÁCE CODEXU

Pracuj systematicky.

## Fáze A – průzkum

Nejprve:

1. prozkoumej celý repozitář,
2. zjisti současný stav projektu,
3. zjisti dostupné soubory,
4. zjisti, zda existuje CSV vzor,
5. zjisti Composer konfiguraci,
6. ověř aktuální verze Nette,
7. identifikuj existující změny, které nesmíš poškodit.

## Fáze B – plán

Vytvoř:

`course/COURSE-PLAN.md`

Ten bude obsahovat:

* výslednou osnovu,
* návaznosti lekcí,
* PHP témata,
* Nette témata,
* výstupy,
* přibližnou délku.

Zkontroluj, že žádný složitější koncept není použit výrazně dříve, než je vysvětlen.

## Fáze C – implementace

Implementuj aplikaci postupně podle lekcí.

Po každé lekci:

1. ověř funkčnost,
2. aktualizuj dokument,
3. zkontroluj synchronizaci dokumentace a kódu,
4. aktualizuj COURSE-MAP.

## Fáze D – audit

Po dokončení projdi kurz ještě jednou jako začátečník.

Hledej především:

* nevysvětlené PHP konstrukce,
* skoky v obtížnosti,
* neúplný kód,
* nefunkční odkazy,
* nekonzistentní názvy,
* zastaralé Nette API,
* bezpečnostní chyby,
* zbytečnou složitost.

Oprav je.

## Fáze E – závěrečná kontrola

Spusť dostupné testy a validace.

Nakonec vytvoř:

`course/FINAL-REPORT.md`

Obsah:

* co bylo vytvořeno,
* verze technologií,
* struktura kurzu,
* struktura aplikace,
* provedené kontroly,
* známá omezení,
* případné doporučené pokračování.

---

# 35. PRIORITY

Při konfliktu priorit dodrž toto pořadí:

1. správnost,
2. bezpečnost,
3. srozumitelnost pro začátečníka,
4. aktuální doporučení Nette,
5. funkčnost aplikace,
6. kvalitní architektura,
7. výkon,
8. elegance,
9. stručnost.

Raději napiš o několik odstavců více než přeskočit princip, bez kterého student pouze mechanicky opisuje kód.

Cílem není studentovi ukázat, jak se co nejrychleji vytvoří CRUD.

Cílem je, aby po dokončení kurzu dokázal vysvětlit, **proč aplikace funguje, kudy prochází požadavek, která část programu za co odpovídá a jakou roli v tom hraje PHP a Nette.**
