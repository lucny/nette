# Lekce 14 – JSON API

**Čas:** 90 minut  
**Výchozí stav:** ProductRepository a import fungují.

## Co dnes vytvoříme

Read-only endpoint `GET /api/products/{code}` v `app/Presentation/Api/ProductsPresenter.php`.

## Co se naučíme

- rozlišit API, endpoint, URL, metodu a status code,
- serializovat řádek do JSON a nastavit `Content-Type`,
- vrátit 200 pro nalezený a 404 pro nenalezený produkt,
- sdílet repository mezi HTML a API.

## Kde jsme skončili

HTML seznam i CSV používají stejnou modelovou vrstvu. Chceme stejná data nabídnout jinému klientovi bez kopírování SQL.

## Nové pojmy

API, endpoint, JSON, serializace, `Content-Type`, status 200/404, read-only.

## PHP princip

Databázový řádek převedeme na asociativní pole. Datum formátujeme přes `DATE_ATOM`, aby klient dostal jednoznačný řetězec. `bool`, `int` a `float` nesmí skončit jako náhodné texty.

## Nette princip

`JsonResponse` nastaví `application/json`. Presenter může nastavit HTTP kód na 404 a odeslat smysluplnou JSON chybu. Route je v `RouterFactory`, ne roztroušená v kódu.

## Jak to funguje

```text
GET /api/products/NB-001 → Router → Api:Products → repository → JsonResponse
```

## Postup krok za krokem

1. Otevři `http://localhost/.../www/api/products/NB-001`. Ulož odpověď a všimni si hlavičky `Content-Type`.
2. Otevři neexistující `/api/products/NO-999`. Ověř HTTP 404 a JSON `{ "error": ..., "code": ... }`.
3. Zkus `curl -i http://localhost/.../www/api/products/NB-001`. Přepínač `-i` zobrazí hlavičky.
4. V presenteru najdi `findByCode()`. API nepoužívá nový SQL dotaz.
5. Diskutuj, že veřejný výukový endpoint je pouze pro čtení. Reálná aplikace by řešila autentizaci, autorizaci, rate limiting a rozsah dat.

## Co se právě stalo

HTML a JSON jsou dva různé výstupy stejné modelové vrstvy. API není automaticky bezpečné jen proto, že vrací JSON; veřejná data musí být záměrně vybraná.

## Experiment

Přidej `fetch()` z prohlížeče nebo konzole DevTools. Porovnej objekt JSON s HTML stránkou a vysvětli, proč API klient nepotřebuje Latte layout.

## Miniúkol

Přidej do JSON pole `stockStatus` s hodnotami `ok`, `low` nebo `empty`. Rozhodni, zda je to modelová informace, nebo prezentační zkratka, a zdůvodni umístění.

## Minikvíz

1. Co je endpoint? **Konkrétní URL operace API.**
2. Jaká metoda je pro tento endpoint? **GET.**
3. Co značí 404? **Požadovaný zdroj nebyl nalezen.**
4. Kdo serializuje pole do JSON? **JSON response/serializační vrstva.**

## Nejčastější chyby

- vrácení HTML chyby místo JSON,
- status 200 pro neexistující produkt,
- zveřejnění password hash nebo interních sloupců,
- duplikace dotazu místo repository,
- chybějící `Content-Type`.

## Kontrolní body

- existující code vrátí JSON a 200,
- neexistující code vrátí JSON a 404,
- stejný repository obslouží HTML i API,
- datum má strojově čitelný formát.

## Shrnutí

API je smlouva mezi klientem a serverem. URL, metoda, status, hlavička a JSON payload musí být předvídatelné. Výstupní formát nemění odpovědnost repository.

## Co bude příště

Provedeme bezpečnostní a výkonnostní audit celé aplikace.

## Stav projektu po lekci

- Funguje `GET /api/products/{code}` s 200/404 a JSON.
- Endpoint je veřejný read-only pouze jako výukový kompromis.
- Přibyl Api presenter a route.

## Poznámka pro učitele

Nechte studenty porovnat Network panel HTML a API. Při nedostatku času vynechte `fetch`, ale nevynechávejte status 404, Content-Type a hranici veřejných dat.
