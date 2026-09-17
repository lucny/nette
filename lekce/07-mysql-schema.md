# Lekce 07 – Relační databáze a MySQL

**Čas:** 90 minut · **Navazuje na:** [lekci 06](06-presenter-routing-latte.md) · **Výsledek:** databáze `nette_products` se schématem a UTF-8 daty.

> **🎯 Cíl lekce**
>
> Vytvoříte tabulky `product` a `user`, importujete bezpečná demonstrační data a SQL dotazem ověříte, že databáze odmítne duplicitní kód produktu.

## Co databáze přináší

Pole v PHP zmizí, jakmile skončí běh programu. Databáze drží data trvale a umí hlídat pravidla i tehdy, když request nepřišel z našeho formuláře.

```text
databáze
  └── tabulka product
        ├── řádek: jeden produkt
        ├── sloupec: code, name, stock, …
        └── omezení: PRIMARY KEY, UNIQUE, index
```

> **🧠 Nejdřív přemýšlej**
>
> Pokud dva lidé odešlou stejný nový kód produktu ve stejný okamžik, stačí kontrola v prohlížeči? Která vrstva musí duplicitu skutečně zakázat?

## Slovník před SQL

| Pojem | Konkrétní význam v projektu |
|---|---|
| databáze | `nette_products`, společný obal tabulek |
| tabulka | `product` nebo `user` |
| řádek | jeden notebook nebo jeden účet |
| primární klíč | `id`, jedinečný identifikátor řádku |
| `UNIQUE` | pravidlo: hodnota se nesmí opakovat |
| index | pomocná datová struktura pro rychlejší hledání |
| `NULL` | chybějící/neznámá hodnota, ne prázdný text a ne nula |

## Soubor se schématem

**📄 Přesná citace části souboru:** `database/schema.sql`

```sql
CREATE TABLE IF NOT EXISTS product (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    code VARCHAR(40) NOT NULL,
    name VARCHAR(160) NOT NULL,
    description TEXT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    price DECIMAL(12, 2) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_product_code (code),
    KEY idx_product_active_created (active, created_at),
    KEY idx_product_name (name)
) ENGINE=InnoDB;
```

Toto je přesná definice tabulky z projektu. `AUTO_INCREMENT` přidělí nové `id`, ale **není** náhradou obchodního kódu `NB-001`; ten má vlastní `UNIQUE` omezení.

### Proč cena není `FLOAT`

`FLOAT` ukládá přibližnou binární hodnotu. Pro měření teploty je to často přijatelné, pro peníze nechceme, aby se při součtech projevila zaokrouhlovací chyba. `DECIMAL(12, 2)` ukládá přesnou desetinnou hodnotu s nejvýše dvanácti číslicemi celkem a dvěma za čárkou.

> **⚠️ Pozor**
>
> SQL schema není náhradou uživatelsky přívětivé validace. Formulář má dát srozumitelnou chybu, databáze musí být poslední autoritou nad integritou dat.

## Bezpečný import UTF-8 seed dat

**📄 Úvod souboru:** `database/seed.sql`

Soubor začíná:

```sql
USE nette_products;
SET NAMES utf8mb4;
```

České názvy produktů jsou uloženy v UTF-8. V terminálu použijte klienta s odpovídajícím kódováním:

```bash
mysql --default-character-set=utf8mb4 -u root < database/schema.sql
mysql --default-character-set=utf8mb4 -u root nette_products < database/seed.sql
```

Příkaz se spouští v kořeni repozitáře. Znak `<` předá obsah souboru přímo klientovi MySQL. Přeposlat text SQL přes nevhodně nastavený PowerShellový řetězec by mohlo poškodit diakritiku.

## Postup krok za krokem

1. Otevřete `database/schema.sql` a bez spuštění si najděte všechny sloupce, které nemohou být `NULL`.
2. Spusťte první importní příkaz. Pokud MySQL vyžaduje heslo roota, zadejte ho jen do lokálního terminálu; nezapisujte jej do souboru ani do historie kurzu.
3. Spusťte seed. Otevřete MySQL klienta příkazem `mysql -u root nette_products`.
4. Spusťte:

```sql
SELECT id, code, name, stock, price, active
FROM product
ORDER BY id;
```

5. Ověřte české znaky v názvu `Klávesnice` a `Myš bez skladu`.
6. Zkuste v testovací databázi vložit druhý řádek s `code = 'NB-001'`. Zapište přesné chybové hlášení `UNIQUE` a testovací řádek případně odstraňte.
7. Prohlédněte si indexy příkazem `SHOW INDEX FROM product;`. Nemusíte znát interní algoritmus indexu; stačí rozumět, proč je `code` indexován a proč index nepřidáváme bez důvodu ke každému sloupci.

## Experiment: nula, prázdný text a `NULL`

Vytvořte si na papír tři příklady: produkt s `stock = 0`, produkt s `description = ''` a hypotetický produkt s `description = NULL`. U každého vysvětlete, co přesně informace znamená. V našem schématu `description` `NULL` být nesmí: prázdný popis je povolený text, ale neznámá hodnota není součástí pravidel aplikace.

## Samostatný úkol

Navrhněte nový produkt se stavem neaktivní a skladem nula. Napište SQL `INSERT` pouze pro svou lokální testovací databázi, ověřte jej `SELECT` a pak řádek smažte podle vlastního kódu. Před spuštěním zkontrolujte, že `DELETE` obsahuje `WHERE code = '…'`; příkaz bez `WHERE` by odstranil všechny produkty.

## Minikvíz

1. Co identifikuje řádek technicky? **Primární klíč `id`.**
2. Proč `UNIQUE(code)`? **Aby databáze nepovolila dva stejné produktové kódy.**
3. Proč používáme `DECIMAL`? **Cena má být přesná desetinná částka.**
4. Co je index? **Datová struktura, která může urychlit vyhledání, ale také něco stojí při zápisu.**

## Kontrolní body a zdroje

- [ ] Existují tabulky `product` a `user`.
- [ ] Seed obsahuje aktivní i neaktivní produkty a správnou češtinu.
- [ ] Duplicitní `code` databáze odmítne.
- [ ] Dokážete vysvětlit `PRIMARY KEY`, `UNIQUE`, `NULL` a `DECIMAL`.

Čtěte [MySQL: CREATE TABLE](https://dev.mysql.com/doc/refman/8.4/en/create-table.html), [datové typy MySQL](https://dev.mysql.com/doc/refman/8.4/en/data-types.html) a [indexy MySQL](https://dev.mysql.com/doc/refman/8.4/en/optimization-indexes.html).

## Stav projektu po lekci

MySQL uchovává produkty a uživatele; schema chrání klíčová pravidla. Nette zatím data nečte – následující lekce propojí databázi s PHP přes `Explorer` a repository.

**Příště:** presenter nebude znát DSN ani SQL; požádá repository, které dostane databázový objekt přes dependency injection.
