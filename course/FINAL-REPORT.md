# Závěrečný report kurzu

## Vytvořeno

- šestnáct navazujících lekcí v `lekce/` plus přípravná lekce 0,
- plán a mapu kurzu v `course/`,
- funkční Nette 3.3 web-project v kořeni repozitáře,
- MySQL schema/seed, bezpečný lokální config a veřejné ukázkové CSV,
- produktový CRUD, databázové filtrování a stránkování,
- login s hashovaným heslem, CSV transakční UPSERT a JSON API.

## Technologie

PHP 8.3+, Composer, Nette web-project 3.3.x, Latte 3.1, Nette Database, Forms, Security, Tracy a MySQL 8.4. Verze jsou zapsané v `composer.lock`; při instalaci může Composer zvolit nejnovější patch v povoleném rozsahu.

## Provedené kontroly

Před odevzdáním spusť `composer validate`, PHP syntax check, Nette Tester, PHPStan a Latte lint podle `course/README.md`. Ručně ověř HTTP seznam, formuláře, POST mazání, import i API 200/404. Připojení k MySQL je prostředí závislé, proto je odděleno do ignorovaného `config/local.neon`.

## Omezení a další kroky

Projekt je výukový: API je read-only a veřejné, uživatelé se nespravují v UI a import pracuje s malým CSV limitem. Pro produkci by následovalo řízení rolí, rate limiting, audit log, detailnější upload policy, deployment konfigurace a integrační testy proti izolované databázi.
