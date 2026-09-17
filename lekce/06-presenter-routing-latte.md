# Lekce 06 – Presenter, routing a Latte

**Čas:** 2 × 45 minut  
**Výchozí stav:** homepage v Nette.

## Co dnes vytvoříme

Presenter `ProductPresenter`, route `/product` a šablonu s dočasnými produkty. Seznam bude oddělený od HTML pomocí MVP.

## Co se naučíme

- rozlišit Model, View a Presenter,
- použít `extends`, `protected`, action a render metodu,
- vytvořit odkaz přes `n:href`,
- použít Latte `{foreach}`, `{if}` a automatický escaping.

## Kde jsme skončili

Nette vykresluje úvodní stránku. Data zatím vznikají v presenteru nebo v testovacím poli.

## Nové pojmy

MVP, presenter, action, `renderDefault()`, Latte, layout, `n:href`, escaping.

## PHP princip

`extends` dědí chování předka. `protected` dovolí přístup třídě a potomkům, ale ne libovolnému kódu. `renderDefault()` je metoda, do které připravíme data pro view.

## Nette princip

Presenter přijme požadavek, zavolá model a vybere odpověď. Latte je šablonovací jazyk, nikoli PHP. Neobsahuje SQL dotazy ani obchodní pravidla.

## Jak to funguje

```text
/product → Router → ProductPresenter::renderDefault()
                    ↓ data
                 default.latte → HTML (escapované) → browser
```

## Postup krok za krokem

1. Vytvoř `app/Presentation/Product/ProductPresenter.php` jako třídu dědící z `Presenter`.
2. Do `renderDefault()` vlož pole produktů. Komentář má vysvětlit, že jde o dočasné řešení, které v lekci 8 nahradí repository.
3. V `app/Presentation/Product/default.latte` použij `{foreach $products as $product}` a vypiš tabulku.
4. Do routeru přidej `product[/<action>][/<id>]`. Zkontroluj, že `/product` míří na `Product:default`.
5. Vlož do názvu text `<b>Test</b>`. Ověř, že Latte ho zobrazí jako text. Raw HTML by vyžadovalo záměrný a zdůvodněný postup.

## Co se právě stalo

Model je zdroj dat, View je Latte a Presenter je prostředník. MVP není tři magické složky; je to rozdělení odpovědnosti, díky kterému můžeme stejná data později poslat jako JSON.

## Experiment

Přesuň podmínku „nízký sklad“ z PHP do Latte a porovnej čitelnost. Potom vysvětli, proč SQL dotaz do Latte nepatří.

## Miniúkol

Přidej do tabulky sloupec Aktivní a odkaz „Detail“. Odkaz vytvoř přes `n:href`, ne ručním zřetězením vstupu.

## Minikvíz

1. Kdo tvoří HTML? **View/Latte.**
2. Kdo má získat data? **Modelová vrstva, ne šablona.**
3. Co dělá `n:href`? **Generuje odkaz přes router.**
4. Je Latte PHP? **Ne.**

## Nejčastější chyby

- špatný namespace a route,
- chybějící template `default.latte`,
- SQL nebo `new` repository přímo v Latte,
- ruční URL `/product/edit/1` místo generovaného odkazu,
- vypnutí escapingu bez vysvětlení.

## Kontrolní body

- `/product` vrací HTTP 200,
- zobrazí se více produktů,
- odkaz se vygeneruje routerem,
- názvy se escapují.

## Shrnutí

Presenter nepatří k databázi ani k HTML. Připraví data a vybere šablonu. Latte vykreslí bezpečný výstup a router centralizuje URL.

## Co bude příště

Dočasné pole nahradíme relační databází MySQL. Navrhneme tabulky, typy, klíče a indexy.

## Stav projektu po lekci

- Funguje seznam dočasných produktů v Nette.
- Data se ještě neukládají trvale.
- Přibyly Product presenter, route a Latte template.

## Poznámka pro učitele

Položte otázku „kdo má tuto odpovědnost?“ u každého řádku. Při nedostatku času vynechte porovnání MVC/MVP, ale nesmí zmizet routing, escaping a zákaz dotazů v Latte.
