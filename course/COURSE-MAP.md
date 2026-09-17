# Mapa kurzu

Začněte v [přípravné lekci](../lekce/0-lekce.md), potom postupujte čísly. Každá lekce navazuje na stav předchozí a obsahuje cíl, vysvětlení PHP i Nette, experiment, samostatný úkol, minikvíz, kontrolní body a ověřené zdroje. Pro spuštění aplikace a úplný studijní portál použijte [README v kořeni repozitáře](../README.md).

| Pořadí | Lekce | Výsledek |
|---:|---|---|
| 0 | [Prostředí](../lekce/0-lekce.md) | ověřený Apache, PHP, MySQL, Composer |
| 1 | [Web a první PHP](../lekce/01-web-a-prvni-php.md) | karta produktu v čistém PHP |
| 2 | [Pole, podmínky, cykly](../lekce/02-pole-podminky-cykly.md) | tabulka produktů v paměti |
| 3 | [Funkce, typy, formulář](../lekce/03-funkce-typy-formular.md) | bezpečný GET filtr |
| 4 | [Objekty a Composer](../lekce/04-objekty-composer.md) | objekt Product a autoloading |
| 5 | [První aplikace v Nette](../lekce/05-prvni-aplikace-v-nette.md) | homepage přes Bootstrap a Latte |
| 6 | [Presenter, routing a Latte](../lekce/06-presenter-routing-latte.md) | Nette seznam dočasných produktů |
| 7 | [MySQL schema](../lekce/07-mysql-schema.md) | tabulky product/user a seed |
| 8 | [Repository a DI](../lekce/08-database-repository-di.md) | čtení přes Nette Database |
| 9 | [Filtrování a stránkování](../lekce/09-seznam-filtrovani-strankovani.md) | databázový seznam |
| 10 | [Nette Forms](../lekce/10-nette-forms-pridani.md) | přidání produktu a PRG |
| 11 | [Editace a mazání](../lekce/11-editace-validace-mazani.md) | UPDATE a bezpečné POST mazání |
| 12 | [Přihlášení](../lekce/12-prihlaseni-security.md) | hashovaná hesla a session |
| 13 | [CSV import](../lekce/13-csv-import.md) | validovaný transakční UPSERT |
| 14 | [JSON API](../lekce/14-json-api.md) | GET endpoint 200/404 |
| 15 | [Bezpečnost a výkon](../lekce/15-bezpecnost-vykon.md) | audit a důkazy z Tracy |
| 16 | [Refactoring a testy](../lekce/16-refactoring-testovani.md) | testy a závěrečná obhajoba |

## Společné materiály

- [Plán kurzu](COURSE-PLAN.md)
- [README kurzu](README.md)
- [schéma databáze](../database/schema.sql)
- [seed data](../database/seed.sql)
- [platný CSV vzor](../database/sample-products.csv)
- [chybný CSV vzor](../database/invalid-products.csv)

## Cílová struktura aplikace

```text
app/
├── Model/Product/       repository, validace, importer
├── Model/User/          repository a authenticator
├── Presentation/        Home, Product, Sign, Import, Api
├── Bootstrap.php
└── Core/RouterFactory.php
database/                schema, seed, CSV vzory
www/                     veřejný index, CSS, JavaScript
```
