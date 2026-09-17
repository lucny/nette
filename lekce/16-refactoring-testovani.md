# Lekce 16 – Refactoring, testování a závěrečná výzva

**Čas:** 2 × 45 minut  
**Výchozí stav:** dokončená aplikace a bezpečnostní audit.

## Co dnes vytvoříme

Test validátoru, checklist celého toku a návrhy rozšíření. Student vysvětlí obě cesty aplikací: HTML a JSON.

## Co se naučíme

- použít automatický test pro čistou část domény,
- ověřit importní validaci a vyhledání kódu rozumným způsobem,
- vysvětlit tok requestu od routeru po databázi a zpět,
- navrhnout rozšíření bez kopírování existující logiky.

## Kde jsme skončili

Máme produktový CRUD, login, CSV UPSERT, API, stránkování a audit. Nyní ověříme, že lze části měnit bez porušení kontraktů.

## Nové pojmy

refactoring, unit test, integration test, regresní chyba, kontrakt, testovací data.

## PHP princip

Čistý `ProductInputValidator` lze testovat bez MySQL, protože dostane pole a vrací chyby. Repository a API jsou integrační hranice: jejich test vyžaduje databázi nebo HTTP server.

## Nette princip

Nette Tester běží příkazem `composer tester`. PHPStan kontroluje typy a Latte lint syntaxi šablon. Test nenahrazuje ruční ověření requestu, ale chrání pravidlo před návratem chyby.

## Jak to funguje

```text
HTTP request → Router → Presenter → Repository → MySQL
                                 └→ Latte → HTML
                                 └→ JsonResponse → JSON
```

## Postup krok za krokem

1. Spusť `composer validate` a `php -l` nad `app/` a `bin/`.
2. Spusť `composer tester`. Projdi `tests/Unit/ProductInputValidator.phpt` a vysvětli, proč nepotřebuje skutečné heslo ani databázi.
3. Spusť `vendor/bin/phpstan analyse`. Každou chybu nejdříve pochop; nepřidávej slepé potlačení.
4. Spusť `vendor/bin/latte-lint app/Presentation`. Ověř šablony.
5. Ručně ověř: login, seznam, filtr, druhou stránku, create, edit, POST delete, import validní/chybný, API 200/404.
6. Prohlédni `git diff` a odstraň komentáře typu „nastavujeme proměnnou“. Ponech komentáře vysvětlující důvod, hranici nebo rollback.

## Co se právě stalo

Testujeme nejlevnější pravidla automaticky a hranice ručně. `ProductRepository` se nepřepisuje pro API; různé prezentace používají stejný model.

## Experiment

Záměrně rozbij regex pro kód a spusť test. Zapiš, jak test selhal, oprav změnu a spusť jej znovu. To je regresní smyčka: změna, důkaz, oprava.

## Miniúkol

Vyber jedno rozšíření a vytvoř návrh odpovědností:

- kategorie produktů,
- řazení s whitelistem sloupců,
- minimální sklad,
- API seznam produktů,
- změna hesla,
- export CSV.

Nedodávej celé řešení. Uveď tabulku, route, presenter, službu/repository, validaci a testovací důkaz.

## Minikvíz

1. Co testuje unit test validátoru? **Pravidlo bez databáze a HTTP.**
2. Kdo rozhoduje, zda URL míří na presenter? **Router.**
3. Kdo vytváří JSON? **API presenter/response, data dodá model.**
4. Proč je vhodný whitelist řazení? **Uživatel nesmí ovlivnit názvem sloupce SQL strukturu.**

## Nejčastější chyby

- test, který jen zopakuje implementaci,
- sdílení reálných hesel v testech,
- refactoring bez kontrolního bodu,
- přidání nové SQL logiky do Latte,
- automatické testy bez ručního ověření HTTP.

## Kontrolní body

- všechny linty a testy projdou,
- `GET /api/products/NB-001` je 200,
- neznámý kód je 404,
- běžný seznam nepřenáší celou tabulku,
- student vysvětlí tok HTML i API.

## Shrnutí

Hotová aplikace je také čitelná, testovatelná a vysvětlitelná. Nejlepší závěrečný výstup není další tlačítko, ale schopnost obhájit odpovědnosti a důkazy.

## Co bude příště

Kurz končí. Další práce má být samostatné rozšíření s vlastním návrhem, testem a bezpečnostním zdůvodněním.

## Stav projektu po lekci

- Funguje kompletní administrační aplikace, CSV import, login a API.
- Přibyl unit test validátoru, lintovací příkazy a závěrečný checklist.
- Doporučená rozšíření zůstávají jako samostatné studentské úkoly bez hotového řešení.

## Poznámka pro učitele

Závěr věnujte vysvětlení toku aplikace. Při nedostatku času lze vynechat PHPStan, ale nesmí se vynechat alespoň jeden automatický test, ruční acceptance checklist a studentské rozšíření.
