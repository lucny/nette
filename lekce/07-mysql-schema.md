# Lekce 07 – Relační databáze a MySQL

**Čas:** 90 minut  
**Výchozí stav:** seznam produktů s dočasnými daty.

## Co dnes vytvoříme

`database/schema.sql` vytvoří databázi `nette_products` a tabulky `product` a `user`. `database/seed.sql` přidá bezpečná demonstrační data.

## Co se naučíme

- rozlišit databázi, tabulku, řádek a sloupec,
- vysvětlit primární klíč, `UNIQUE`, `NULL` a index,
- zdůvodnit `DECIMAL` pro cenu,
- spustit schema a seed a ověřit řádky SQL dotazem.

## Kde jsme skončili

Nette umí vykreslit produkty, ale po restartu procesu se data ztratí.

## Nové pojmy

relační databáze, SQL, `PRIMARY KEY`, `UNIQUE`, index, `BOOLEAN`, `DECIMAL`, `DATETIME`, `NULL`.

## PHP princip

PHP dnes jen spouští SQL klientem. Důležitá hranice: databázové schéma je zdroj pravdy pro datové typy a omezení, PHP validace je uživatelsky přívětivá první kontrola.

## Nette princip

Připojení zatím přidáme až v lekci 8. Nyní připravujeme transparentní SQL místo skryté magie.

## Jak to funguje

```text
schema.sql → MySQL server → product/user tabulka → SELECT → ověřený výsledek
```

## Postup krok za krokem

1. Otevři `database/schema.sql`. Najdi `PRIMARY KEY` a `UNIQUE KEY uq_product_code`.
2. Spusť `mysql -u root < database/schema.sql` v prostředí, kde je MySQL dostupné.
3. Spusť `mysql -u root nette_products < database/seed.sql`.
4. Ověř `SELECT code, name, price FROM product ORDER BY id;`.
5. Zkus vložit stejný `code` podruhé. Databáze jej odmítne; aplikace se nesmí spoléhat pouze na kontrolu v prohlížeči.
6. Porovnej `DECIMAL(12,2)` a `FLOAT`. Cena je přesná částka, nikoli přibližná fyzikální hodnota.

## Co se právě stalo

Index pomáhá hledat, ale není náhradou za logiku. `UNIQUE` brání duplicitě na úrovni databáze. `created_at` a `updated_at` umožní vysvětlit, kdy řádek vznikl a měnil se.

## Experiment

Odstraň z kopie schématu unikátní index a vlož dva stejné kódy. Zapiš, jak se změní integrita dat, a index vrať.

## Miniúkol

Navrhni třetí produkt s nulovým skladem a neaktivní stavem. Napiš, které sloupce nesmí být `NULL` a proč.

## Minikvíz

1. Co identifikuje řádek? **Primární klíč.**
2. Proč `UNIQUE(code)`? **Aby kód nebyl duplicitní.**
3. Proč ne `FLOAT` pro cenu? **Může přinášet zaokrouhlovací nepřesnosti.**
4. Co je index? **Datová struktura urychlující vyhledávání podle sloupce/sloupců.**

## Nejčastější chyby

- ruční SQL bez výběru databáze,
- opomenutí `UNIQUE`,
- ukládání ceny jako textu nebo `FLOAT`,
- seed spuštěný vícekrát bez rozmyslu,
- záměna `NULL` a prázdného řetězce.

## Kontrolní body

- tabulky existují,
- seed vrátí aktivní i neaktivní produkty,
- kód je unikátní,
- dotaz vrátí cenu se dvěma desetinnými místy.

## Shrnutí

Databáze drží trvalá data a sama vynucuje důležitá omezení. Schéma je didakticky čitelné a připravuje nás na `Nette\Database\Explorer`.

## Co bude příště

Nastavíme lokální připojení, vytvoříme `ProductRepository` a vysvětlíme dependency injection bez magie.

## Stav projektu po lekci

- Funguje MySQL schema a seed.
- Nette seznam ještě nemusí být připojený k databázi.
- Přibyly `database/schema.sql` a `database/seed.sql`.

## Poznámka pro učitele

Nechte studenty předvést porušení `UNIQUE`. Při nedostatku času lze vynechat podrobný indexový plán, ale nevynechávejte `DECIMAL`, primární klíč a skutečné ověření v MySQL.
