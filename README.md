# Nette od prvního PHP k databázové aplikaci

Praktický český kurz pro studenty, kteří zatím neznají PHP ani Nette. Nevede k mechanickému opsání hotového CRUDu: v každé lekci nejdřív zjistíte, **jaký problém řeší čisté PHP**, a teprve potom uvidíte, kterou opakovanou práci převezme Nette.

Na konci budete umět vysvětlit cestu požadavku od prohlížeče přes router, presenter a databázi až po HTML nebo JSON odpověď. Vytvoříte administraci produktů s přihlášením, vyhledáváním, stránkováním, formuláři, bezpečným mazáním, importem CSV a read-only API.

> **🧭 Jak číst kurz**
>
> **🎯 Cíl** popisuje měřitelný výsledek, **🧠 Nejdřív přemýšlej** vás zastaví před řešením, **📄 Soubor** označuje přesné místo v repozitáři, **🔎 Ověření** říká, co má být pozorovatelné, a **⚠️ Pozor** vymezuje typickou chybu nebo bezpečnostní hranici. Kód označený jako *fragment* nevkládejte místo celého souboru; vždy je uvedeno, kam patří.

## Začněte tady

1. Připravte počítač podle [lekce 0](lekce/0-lekce.md). Je určena pro Windows a Laragon a obsahuje diagnostiku instalace.
2. Projděte kapitoly v uvedeném pořadí. Každá navazuje na předchozí a obsahuje experiment, samostatný úkol, minikvíz a kontrolní body.
3. První tři lekce jsou záměrně čisté PHP. Teprve potom začíná Nette.

> **⚠️ Bezpečnostní pravidlo kurzu**
>
> Pracujte jen s lokální databází, demonstračními CSV soubory a výukovým účtem. Heslo `vyukove-heslo` je veřejný příklad, nikdy jej nepoužívejte mimo tento projekt. Do Gitu nepatří `config/local.neon` ani žádné skutečné heslo.

## Mapa kapitol

| Krok | Kapitola | Co po ní bezpečně umíte ověřit |
|---:|---|---|
| 0 | [Příprava prostředí](lekce/0-lekce.md) | PHP, Composer, Apache a MySQL skutečně běží |
| 1 | [Web a první PHP](lekce/01-web-a-prvni-php.md) | PHP na serveru vytvoří HTML kartu produktu |
| 2 | [Pole, podmínky a cykly](lekce/02-pole-podminky-cykly.md) | z pole produktů vznikne filtrovaná tabulka |
| 3 | [Funkce, typy a HTTP formulář](lekce/03-funkce-typy-formular.md) | GET filtr pracuje s bezpečným vstupem |
| 4 | [Objekty a Composer](lekce/04-objekty-composer.md) | objekt má stav, metodu a autoloading |
| 5 | [První aplikace v Nette](lekce/05-prvni-aplikace-v-nette.md) | znáte tok `index.php → Bootstrap → router → presenter → Latte` |
| 6 | [Presenter, routing a Latte](lekce/06-presenter-routing-latte.md) | Nette vykreslí seznam z presenteru a odkazy přes router |
| 7 | [MySQL schema](lekce/07-mysql-schema.md) | tabulky, klíče, indexy a UTF-8 seed data existují |
| 8 | [Nette Database a DI](lekce/08-database-repository-di.md) | repository načte data přes `Explorer` bez tajemství v Gitu |
| 9 | [Filtrování a stránkování](lekce/09-seznam-filtrovani-strankovani.md) | databáze provede `WHERE` a `LIMIT` |
| 10 | [Nette Forms](lekce/10-nette-forms-pridani.md) | validovaný POST vytvoří produkt a přesměruje na seznam |
| 11 | [Editace a mazání](lekce/11-editace-validace-mazani.md) | editace vrací 404 a mazání vyžaduje POST |
| 12 | [Přihlášení a security](lekce/12-prihlaseni-security.md) | hashované heslo chrání administraci přes session |
| 13 | [CSV import](lekce/13-csv-import.md) | import validuje hlavičku i řádky a provádí UPSERT |
| 14 | [JSON API](lekce/14-json-api.md) | endpoint vrací JSON s HTTP 200 nebo 404 |
| 15 | [Bezpečnost a výkon](lekce/15-bezpecnost-vykon.md) | audit opíráte o skutečný request a Tracy |
| 16 | [Refactoring a testy](lekce/16-refactoring-testovani.md) | test, PHPStan, Latte lint a ruční scénář pokrývají aplikaci |

Podrobnější orientaci mezi tématy obsahuje [plán kurzu](course/COURSE-PLAN.md) a [mapa souborů](course/COURSE-MAP.md).

## Zprovoznění referenční aplikace

Tyto kroky spustí hotový studijní stav. Nejsou náhradou lekcí: každá kapitola vysvětluje, proč daný soubor a příkaz existují.

### 1. Nainstalujte PHP závislosti

V kořeni repozitáře spusťte:

```bash
composer install
```

`composer.lock` určuje přesné verze, proto pro naklonovaný projekt používáme `install`, ne bezdůvodné `update`. Složka `vendor/` je znovu vytvořitelná a není verzována.

### 2. Nastavte lokální databázi

Zkopírujte `config/local.neon.example` na `config/local.neon` a upravte pouze údaje své lokální MySQL. Tento soubor je v `.gitignore`.

> **⚠️ Pozor na Windows a češtinu**
>
> SQL soubory jsou UTF-8. Použijte přepínač `--default-character-set=utf8mb4`; nepřeposílejte jejich text přes PowerShellový řetězec, který by mohl změnit kódování.

```bash
mysql --default-character-set=utf8mb4 -u root < database/schema.sql
mysql --default-character-set=utf8mb4 -u root nette_products < database/seed.sql
```

### 3. Vytvořte výukový účet

```bash
php bin/create-user.php student@example.test vyukove-heslo
```

Skript uloží hash, nikoli otevřené heslo. Přihlaste se přes `/sign` nebo `/sign/in`.

### 4. Spusťte web

Document root Apache nastavte na `www/`. V Laragonu otevřete projekt přes jeho lokální `.test` adresu. Pro krátkou lokální kontrolu lze použít také:

```bash
php -S 127.0.0.1:8088 -t www
```

Pak otevřete `http://127.0.0.1:8088/`.

## Kontrola před odevzdáním

```bash
composer validate
composer tester
vendor/bin/phpstan analyse
vendor/bin/latte-lint app/Presentation
```

Ručně projděte přihlášení, vyhledávání, druhou stránku seznamu, vytvoření, editaci, POST mazání, [platný CSV vzor](database/sample-products.csv), [chybný CSV vzor](database/invalid-products.csv) a API `/api/products/NB-001` i neexistující kód.

## Ověřené zdroje pro další čtení

Odkazy níže byly ověřeny 17. 9. 2026. Vedou přednostně na oficiální dokumentaci nástroje nebo standardu, nikoli na neudržované úryvky kódu.

- [PHP Language Reference](https://www.php.net/manual/en/langref.php) — syntaxe, typy, pole, funkce a objekty.
- [Composer: Basic usage](https://getcomposer.org/doc/01-basic-usage.md) — `install`, `update`, lock soubor a autoloading.
- [Nette: How applications work](https://doc.nette.org/en/application/how-it-works) — request, router, presenter a odpověď.
- [Nette: Forms](https://doc.nette.org/en/forms) — komponenty formulářů a validace.
- [Nette: Database Explorer](https://doc.nette.org/en/database/explorer) — `table()`, `where()`, řazení a stránkování.
- [Nette: Password hashing](https://doc.nette.org/en/security/passwords) — bezpečná práce s hesly.
- [MySQL 8.4 Reference Manual](https://dev.mysql.com/doc/refman/8.4/en/) — SQL, datové typy, indexy a transakce.

## Struktura repozitáře

```text
lekce/       studentské kapitoly v pořadí 0–16
examples/    malé čisté PHP programy z prvních lekcí
app/         presentery, repository, import a security
config/      konfigurace; local.neon zůstává jen na vašem počítači
database/    schema, seed a CSV vzory
www/         jediný veřejný adresář aplikace
tests/       automatizované kontroly pravidel
course/      plán, mapa a závěrečný report
```
