# Plán kurzu Nette Framework pro začátečníky

Kurz používá jako přípravný krok `../lekce/0-lekce.md` a šestnáct navazujících lekcí. Každá lekce zavádí nejvýše jednu významnou novou vrstvu a končí ověřitelným stavem projektu.

| Lekce | Téma | PHP | Nette / výstup | Čas |
|---:|---|---|---|---:|
| 0 | prostředí Laragon | příkazová řádka | Apache, PHP, MySQL, Composer | 90 min |
| 1 | web a první PHP | proměnné, typy, echo | čisté PHP | 90 min |
| 2 | produkty v paměti | pole, podmínky, foreach | čisté PHP tabulka | 90 min |
| 3 | filtr a HTTP | funkce, typy, GET/POST | ruční formulář | 2×45 min |
| 4 | objekty a Composer | třída, namespace, autoloading | web-project | 2×45 min |
| 5 | první Nette aplikace | dědičnost v praxi | Bootstrap, DI, router, Latte | 90 min |
| 6 | presenter a Latte | `extends`, `protected` | MVP a dočasný seznam | 2×45 min |
| 7 | databáze | SQL jako vstup do modelu | MySQL schema a seed | 90 min |
| 8 | repository a DI | property promotion, návratové typy | Nette Database Explorer | 2×45 min |
| 9 | seznam | typované parametry | WHERE, LIMIT, Paginator, Tracy | 2×45 min |
| 10 | přidání | callable, callback | Nette Forms, INSERT, PRG | 2×45 min |
| 11 | editace a mazání | URL parametr, výjimka | UPDATE, POST, `Requires`, JS UX | 2×45 min |
| 12 | přihlášení | interface, implements, try/catch | Nette Security, hash, session | 2×45 min |
| 13 | CSV import | stream, parser, transformace | upload, transakce, UPSERT | 2×45 min |
| 14 | API | asociativní data, datum | JSON, 200/404, Content-Type | 90 min |
| 15 | bezpečnost a výkon | kontextové zpracování dat | XSS, SQLi, CSRF, indexy, Tracy | 2×45 min |
| 16 | refactoring a testy | testovatelnost | Tester, PHPStan, acceptance checklist | 2×45 min |

## Návaznosti

`lekce/01` až `lekce/03` učí jazyk na stejném problému produktů. `lekce/04` vysvětlí objekty a Composer dříve, než se objeví Nette presenter. Od `lekce/07` je zdrojem pravdy MySQL. Repository z `lekce/08` používají HTML seznam, formuláře, importer i API; tím se demonstruje znovupoužití modelové vrstvy.

## Cílový stav

Student vytvoří bezpečnou, stránkovanou administraci produktů s přihlášením, Nette Forms, CSV UPSERT importem a read-only JSON API. Umí vysvětlit tok requestu, odpovědnosti vrstev a základní bezpečnostní i výkonnostní limity.
