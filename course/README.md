# Kurz Nette Framework pro začátečníky

Kurz je pro studenty střední školy, kteří znají základní práci s počítačem, ale nepředpokládá znalost PHP ani frameworků. Výsledkem je jednoduchá administrační aplikace produktů: login, seznam s filtrem a stránkováním, create/edit, bezpečné POST mazání, CSV UPSERT import a read-only JSON API.

## Prostředí

- Windows + Laragon nebo ekvivalentní Apache/PHP/MySQL prostředí,
- PHP 8.3+, Composer 2, MySQL 8.4,
- Visual Studio Code,
- aktuální řada Nette 3.3.x, Latte 3.1, Nette Database, Forms, Security a Tracy.

Nette Application 3.3 podporuje PHP 8.3–8.5 a aktuální dokumentace používá mapování `App\Presentation\*\**Presenter`. Základní instalace vychází z oficiálního [Nette web-projectu](https://doc.nette.org/en/installation) a [quickstartu](https://doc.nette.org/en/quickstart).

## Student: instalace a spuštění

1. Naklonuj repozitář a otevři jeho kořen ve VS Code.
2. Ověř `php -v`, `composer --version`, `mysql --version`.
3. Spusť `composer install`.
4. Zkopíruj `config/local.neon.example` jako `config/local.neon` a nastav lokální DSN. Soubor je ignorovaný Gitem; skutečné heslo do repozitáře nepatří.
5. Spusť `mysql --default-character-set=utf8mb4 -u root < database/schema.sql` a potom `mysql --default-character-set=utf8mb4 -u root nette_products < database/seed.sql`. Parametr je důležitý, protože seed obsahuje českou diakritiku.
6. Nastav document root Apache na `www/`, nebo otevři lokální adresu projektu přes Laragon.
7. Vytvoř výukový účet: `php bin/create-user.php student@example.test vyukove-heslo`. Nepoužívej toto heslo mimo lokální výuku.
8. Otevři homepage a přihlas se přes `/sign/in`.

## Ověření

```text
composer validate
composer tester
vendor/bin/phpstan analyse
vendor/bin/latte-lint app/Presentation
```

Pak ručně ověř seznam, filtr, stránkování, create, edit, POST delete, import `database/sample-products.csv`, chybový import a API `/api/products/NB-001` i neexistující kód.

## Učitel

Začněte `lekce/0-lekce.md` a postupujte přes [mapu kurzu](COURSE-MAP.md). Před každou lekcí ověřte kontrolní body předchozí. Studentům nepředávejte finální aplikaci jako černou skříňku: významné bloky jsou v lekcích vysvětlené a komentáře v kódu vysvětlují důvody, hranice a bezpečnost.

## Bezpečnostní hranice

Používejte jen lokální školní databázi a fiktivní účty. CSV soubory v repozitáři jsou demonstrační. Read-only API je veřejné jen kvůli výuce; v reálné aplikaci by se posuzovala autentizace, autorizace, rate limiting a rozsah zveřejněných údajů.
