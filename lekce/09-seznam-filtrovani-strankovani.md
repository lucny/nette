# Lekce 09 – Seznam produktů, filtrování a stránkování

**Čas:** 2 × 45 minut  
**Výchozí stav:** repository umí číst z MySQL.

## Co dnes vytvoříme

Administrační tabulku se stavem, kódem, názvem, skladem, cenou, filtrem a stránkováním. V `ProductRepository::search()` se omezení použijí před `LIMIT`.

## Co se naučíme

- filtrovat podle kódu/názvu a stavu,
- vysvětlit `WHERE`, `LIMIT` a offset v principu,
- používat `Paginator`,
- přečíst dotaz v Tracy a porovnat správnou a špatnou variantu.

## Kde jsme skončili

Repository načítá jednotlivé produkty. Seznam zatím nemá řízený počet řádků.

## Nové pojmy

selection, `WHERE`, `LIKE`, `COUNT`, `LIMIT`, stránka, offset, Paginator, index.

## PHP princip

Presenter převede query parametry na očekávané typy a předá je repository. Latte pouze projde výslednou selection. Nepřidáváme do PHP vlastní pole všech produktů.

## Nette princip

Nette Database sestaví parametrizovaný SQL dotaz z řetězce metod. `Paginator` pomáhá spočítat stránku; samotná data zůstanou v databázové selection.

## Jak to funguje

```text
q/status/page → repository → WHERE → COUNT + LIMIT → Latte tabulka
```

## Postup krok za krokem

1. Otevři `/product`. Zadej do hledání `note`; zkontroluj zachování hodnoty po odeslání.
2. Přepni stav na aktivní/neaktivní. Porovnej počet výsledků.
3. V `ProductRepository::search()` najdi `selection->where(...)`, `count()` a `page(...)`. Komentář u změny má vysvětlit, proč filtr patří před stránkování.
4. Naplň tabulku alespoň 20 řádky pomocí bezpečných demonstračních dat, aby šla vidět druhá stránka.
5. Otevři Tracy Database panel. Zapiš, že dotaz načetl jen řádky aktuální stránky, zatímco počet je zvláštní dotaz.

## Co se právě stalo

Špatná varianta je `SELECT *` → všechno do PHP → filtrovat pole. Správná varianta posílá podmínku databázi. U velkého katalogu rozhoduje rozdíl mezi deseti a statisíci načtených řádků.

## Experiment

Do kopie repository dočasně vlož načtení všech řádků a PHP filtr. Porovnej Tracy čas a počet objektů s ostrou verzí. Změnu necommituj.

## Miniúkol

Přidej filtr „sklad 0“. Nejdříve napiš, zda potřebuje nový index, a proč. Ukaž, kde se hodnota validuje.

## Minikvíz

1. Co omezuje počet načtených řádků? **Databázový `LIMIT`/`page()`.**
2. Kde má proběhnout hledání podle názvu? **V SQL přes repository.**
3. Je `count()` počet objektů načtených do PHP? **Ne, je to počet odpovídajících řádků v databázi.**
4. Proč index není automaticky pro každý sloupec? **Zvyšuje cenu zápisů a zabírá místo.**

## Nejčastější chyby

- stránkování až po načtení všeho,
- ztráta filtru při odkazu na další stránku,
- přímé vložení query do SQL,
- `LIKE '%text%'` bez vysvětlení indexových omezení,
- zobrazení čísla stránky bez ověření rozsahu.

## Kontrolní body

- `/product` vrací HTTP 200,
- filtr a stránka se zachovají v URL,
- Tracy ukazuje `WHERE` a `LIMIT`,
- tabulka neobsahuje neomezený počet řádků.

## Shrnutí

Filtrování a stránkování patří do databázové vrstvy. Presenter řídí request, repository query a Latte výpis.

## Co bude příště

Vytvoříme produkt přes Nette Form a oddělíme serverovou validaci od klientského pohodlí.

## Stav projektu po lekci

- Funguje administrační seznam, hledání, stavové filtry a stránkování.
- Přidávání a editace ještě řešíme v další lekci.
- Změněny Product presenter, repository a template.

## Poznámka pro učitele

Tracy panel je dobrý okamžik, kdy studenti vidí, že abstrakce vytváří skutečné SQL. Při nedostatku času vynechte experiment se záměrně špatnou variantou, ale nevynechávejte kontrolu `LIMIT`.
