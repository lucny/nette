# Lekce 11 – Editace, validace a mazání

**Čas:** 2 × 45 minut  
**Výchozí stav:** produkt lze přidat přes Nette Form.

## Co dnes vytvoříme

Akci editace se stejným formulářem a mazání přes POST signal `handleDelete()`. Před odesláním použijeme `confirm()` jako UX prvek.

## Co se naučíme

- načíst počáteční hodnoty pomocí URL parametru,
- rozlišit INSERT, UPDATE a HTTP 404,
- vysvětlit, proč GET nemá mazat,
- použít `#[Requires(methods: 'POST')]` a same-origin ochranu.

## Kde jsme skončili

Formulář vytváří nové produkty. URL zatím neumí bezpečně vyjádřit, který produkt upravujeme.

## Nové pojmy

ID, URL parametr, `404`, UPDATE, POST signal, same-origin, UX, `confirm()`.

## PHP princip

`int $id` je typovaný parametr. Nette jej převádí z URL a při neplatné hodnotě request odmítne. Přesto ověřujeme, zda řádek existuje, protože platné číslo nemusí znamenat existující produkt.

## Nette princip

`#[Requires(methods: 'POST')]` říká, že signál se nesmí volat přes GET. Signály v Nette mají same-origin ochranu; potvrzovací dialog pouze snižuje počet omylů a není bezpečnostní bariéra.

## Jak to funguje

```text
GET /product/edit/4 → načíst row → setDefaults → Form
POST signal delete! → Requires POST + same-origin → DELETE → redirect
```

## Postup krok za krokem

1. V `actionEdit(int $id)` načti `$this->products->find($id)`. Při `null` použij `$this->error(...)`, které vytvoří 404.
2. Nastav `editingId` a `setDefaults()` pro všech šest polí.
3. V callbacku rozliš `editingId === null` a zavolej `update($id, $values)`.
4. V tabulce použij pomocný POST form a tlačítko s URL signálu `delete!`. Nezapisuj `<a href=".../delete/4">`.
5. Přidej `data-confirm` a v `www/assets/app.js` volej `window.confirm`. Při odmítnutí zruš submit.
6. Zkus zadat `/product/delete/4` v adresním řádku. Neexistující GET mazací akce není veřejný kontrakt aplikace.

## Co se právě stalo

GET je bezpečné čtení a může se opakovat, kešovat nebo načíst robotem. Mazání mění stav, proto patří na POST a server musí kontrolovat metodu i původ. JavaScript pouze zlepšuje UX; útočník jej může vypnout.

## Experiment

Vypni JavaScript a odešli POST mazání. Ověř, že mazání stále funguje bezpečně. Potom zkus GET a vysvětli, co ochranu zastaví.

## Miniúkol

Přidej samostatnou stránku 404 pro neexistující ID a uveď, proč nesmí být chyba vyřešena tichým prázdným formulářem.

## Minikvíz

1. Co dělá `setDefaults()`? **Naplní formulář počátečními hodnotami.**
2. Je `confirm()` bezpečnost? **Ne, je to UX.**
3. Která metoda mění data? **POST (v tomto kurzu).**
4. Co má API vrátit pro neexistující produkt? **404 a smysluplnou odpověď.**

## Nejčastější chyby

- mazání přes GET odkaz,
- důvěra v JavaScript,
- editace bez kontroly existence,
- UPDATE všech řádků kvůli chybějícímu `where`,
- zapomenutý redirect po změně.

## Kontrolní body

- editace načte správný produkt,
- neexistující ID vrátí 404,
- mazání vyžaduje POST,
- vypnutí JS neodstraní serverovou ochranu.

## Shrnutí

Editace znovu používá komponentu a UPDATE. Mazání je stavová změna: POST, same-origin a serverová autorizace; `confirm()` je jen pohodlí.

## Co bude příště

Přidáme autentizaci, uživatele v databázi a bezpečné hashování hesel.

## Stav projektu po lekci

- Funguje přidání, editace a mazání produktu.
- Administrace je připravená na ochranu přihlášením.
- Přibyly edit/create šablony, POST signal a vanilla JS potvrzení.

## Poznámka pro učitele

Nechte studenty sami navrhnout škodlivý GET request a pak ho odmítněte. Při nedostatku času lze vynechat vzhled 404, nesmí se přeskočit `Requires`, POST a vysvětlení, že JS není bezpečnost.
