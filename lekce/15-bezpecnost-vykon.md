# Lekce 15 – Bezpečnost a výkon celé aplikace

**Čas:** 2 × 45 minut  
**Výchozí stav:** kompletní aplikace s CRUD, loginem, importem a API.

## Co dnes vytvoříme

Auditní protokol a několik opravených variant. Nejde o novou velkou funkci, ale o schopnost vysvětlit, proč stávající řešení není jen náhodná posloupnost příkazů.

## Co se naučíme

- najít XSS, SQL injection, CSRF/same-origin a slabé zacházení s heslem,
- ověřit upload, CSV, session a veřejný adresář,
- vysvětlit index, stránkování, `LIMIT` a N+1,
- použít Tracy pro důkaz o dotazech místo dojmu.

## Kde jsme skončili

Všechny hlavní funkce fungují. Nyní hledáme, co by se stalo s nedůvěryhodným vstupem, velkou tabulkou nebo cizím requestem.

## Nové pojmy

XSS, SQL injection, CSRF, same-origin, session, rate limiting, N+1, index, production mode.

## PHP princip

Bezpečnostní pravidlo je kontextové: HTML výstup escapujeme, SQL hodnoty parametrizujeme, upload omezuje velikost a obsah, heslo hashuje specializovaná funkce. Jedna univerzální funkce nenahradí správný kontext.

## Nette princip

Latte escapuje výstup, Nette Database parametrizuje dotazy, Form kontroluje vstupy, `#[Requires]` omezuje metodu a same-origin a Tracy v produkci nesmí odhalovat citlivé údaje.

## Jak to funguje

```text
nedůvěryhodný request → validace + autorizace → model → parametrizovaný dotaz
                                      ↓
                         bezpečný HTML/JSON výstup
```

## Postup krok za krokem

1. Vlož do názvu produktu `<script>alert(1)</script>` a ověř, že Latte zobrazí text.
2. Vypiš, kde se query hodnota dostane do `where()`. Najdi, že není slepena do SQL.
3. Vypni JavaScript a vyzkoušej POST mazání. Zkontroluj, že ochrana je serverová.
4. Ověř, že `www/` nepublikuje `config`, `app`, `database` ani `vendor`.
5. Nahraj příliš velký nebo neplatný soubor. Zkontroluj stav uploadu, velikost, MIME a chyby řádků.
6. Tracy Database panelem porovnej stránkovaný dotaz s variantou, která by načítala vše. Zapiš počet dotazů a řádků.
7. Přepni debug mode pouze podle prostředí učitele; produkce nesmí ukazovat stack trace, DSN ani hesla.

## Co se právě stalo

Bezpečnost je průběžná vlastnost: heslo, formulář, upload, SQL i výstup mají vlastní hranici. Výkon není mikrooptimalizace; největší rozdíl udělá neposílat desetitisíce řádků do PHP a mít vhodné indexy.

## Experiment

Vytvoř tabulku auditních zjištění: hrozba, vstup, ochrana, důkaz, omezení. Jedno zjištění musí uvést, co ochrana nedokazuje, například že `confirm()` není autorizace.

## Miniúkol

Navrhni bezpečnou úpravu API, kdyby produktová data nebyla veřejná. Uveď autentizaci, autorizaci, rate limiting a minimální rozsah JSON.

## Minikvíz

1. Chrání confirm před útokem? **Ne.**
2. Co chrání `where('code', $code)`? **Parametrizace před SQL injection.**
3. Co je N+1? **Jeden dotaz na seznam a další dotaz pro každý řádek.**
4. Kde je document root? **`www/`.**

## Nejčastější chyby

- raw výstup bez důvodu,
- ruční slepování SQL,
- plaintext hesla v seed datech,
- veřejný debugger,
- načtení celé tabulky kvůli stránkování,
- přesvědčení, že MIME typ je důkaz obsahu.

## Kontrolní body

- XSS pokus se nevykoná,
- GET nemění data,
- DB dotaz používá parametry,
- seznam je stránkovaný a indexovaný,
- produkční režim neukazuje citlivosti.

## Shrnutí

Ochrana musí být na serveru a v odpovědném kontextu. Výkon začíná architekturou dotazu, ne kosmetickou změnou PHP cyklu.

## Co bude příště

Uděláme závěrečný refactoring, spustíme testy a připravíme samostatná rozšíření bez hotových řešení.

## Stav projektu po lekci

- Funkce aplikace zůstaly stejné, ale máme auditní postup a ověřené bezpečnostní hranice.
- Známé limity: veřejné read-only API je pouze školní volba; produkce potřebuje další politiku přístupu.
- Doplněny bezpečnostní a výkonnostní poznámky v kódu a dokumentaci.

## Poznámka pro učitele

Audit dělejte na skutečných requestech a Tracy, ne jen definicích. Při nedostatku času vynechte N+1, nikoli XSS, SQL injection, hesla a POST mazání.
