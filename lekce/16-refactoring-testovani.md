# Lekce 16 – Refactoring a testování: změna potřebuje důkaz

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 15](15-bezpecnost-vykon.md) · **Výsledek:** spustíte automatické kontroly, přečtete jejich význam, ručně ověříte celé uživatelské toky a navrhnete rozšíření bez kopírování logiky.

> **🎯 Cíl lekce**
>
> Umíte rozhodnout, co ověří rychlý unit test, co vyžaduje databázi a HTTP request a proč se nástroj na typy nerovná testu. Na konci kurzu obhájíte cestu HTML i JSON odpovědi konkrétními soubory a důkazy.

## Proč nestačí „stránka se mi načetla“

Jedno ruční kliknutí často ověří pouze šťastnou cestu. Neodhalí třeba zápornou cenu, chybějící produkt, špatnou Latte proměnnou nebo změněný typ metody. Naopak test, který nic nežije mimo jednu funkci, nemůže dokázat, že router skutečně obslouží URL.

Použijeme více vrstev důkazu:

```text
nejrychlejší a nejužší
        │
        ├─ PHP syntax        → dokáže PHP soubory načíst?
        ├─ unit test         → platí čisté pravidlo validace?
        ├─ PHPStan           → neodporují si deklarované typy a práce s nimi?
        ├─ Latte lint        → jsou šablony syntakticky platné?
        └─ ruční HTTP tok    → spolupracují prohlížeč, router, session a databáze?
        │
nejpomalejší, ale nejbližší uživateli
```

Každá vrstva zachytí jiný druh chyby. Dobrá zpráva je, že nemusíme simulovat prohlížeč, abychom ověřili regulární výraz pro kód produktu.

> **🧠 Nejdřív přemýšlej**
>
> Kdyby validátor omylem povolil kód `špatně`, potřebujete k odhalení chyby MySQL, přihlášení a prohlížeč? Nebo stačí zavolat jednu PHP třídu s jedním polem vstupu?

## Postup krok za krokem: 1. Unit test – malá třída, rychlý a přesný důkaz

`ProductInputValidator` je vhodný pro unit test. Přijme obyčejné asociativní pole a vrátí seznam chyb. Nemá konstruktorový argument pro databázi, neposílá HTTP odpověď a neotevírá soubor. To je znak izolovatelné logiky.

**📄 Úplný soubor:** `tests/Unit/ProductInputValidator.phpt`

```php
<?php

require __DIR__ . '/../bootstrap.php';

use App\Model\Product\ProductInputValidator;
use Tester\Assert;

$validator = new ProductInputValidator;

Assert::same([], $validator->validate([
	'code' => 'NB-001',
	'name' => 'Notebook',
	'description' => 'Ukázka',
	'stock' => 3,
	'price' => 18990.0,
]));

$errors = $validator->validate([
	'code' => 'špatně',
	'name' => '',
	'description' => '',
	'stock' => -1,
	'price' => -5,
]);

Assert::count(4, $errors);
```

`require` načte společný testovací bootstrap. `use` zkrátí plně kvalifikované názvy tříd. `new ProductInputValidator` vytvoří testovaný objekt – bez frameworkového containeru, protože jej tato třída nepotřebuje.

`Assert::same([], …)` požaduje přesně prázdné pole: platný vstup nemá chyby. `Assert::count(4, $errors)` neporovnává věty uživatelských chyb, ale stabilní vlastnost – tento konkrétní chybný vstup poruší čtyři pravidla. Test neříká, že uživatelské rozhraní funguje; říká přesně to, co skutečně ověřil.

## 2. Spusťte kontroly po jedné a čtěte jejich účel

Otevřete terminál v kořeni repozitáře a postupujte v tomto pořadí:

```bash
composer validate --no-check-publish
composer tester
vendor/bin/phpstan analyse --no-progress
vendor/bin/latte-lint app/Presentation
```

| Příkaz | Co kontroluje | Co sám nezaručuje |
|---|---|---|
| `composer validate --no-check-publish` | zda `composer.json` odpovídá formátu Composeru | že se PHP aplikace spustí |
| `composer tester` | testy z adresáře `tests/` přes skript z `composer.json` | že funkce používá prohlížeč správně |
| `phpstan analyse` | statickou práci s typy a některé nedosažitelné či chybné konstrukce | že databáze obsahuje správná data |
| `latte-lint app/Presentation` | syntaxi Latte šablon | že uživatel má oprávnění akci udělat |

### Jak reagovat na selhání

1. Přečtěte první hlášený soubor a řádek, ne jen poslední řádek výpisu.
2. Popište vlastními slovy, co nástroj očekával a co skutečně našel.
3. Otevřete malý související úsek zdrojového kódu.
4. Opravte příčinu, nikoli jen viditelný následek nebo hlášení skryté potlačením.
5. Spusťte nejmenší relevantní kontrolu znovu a až pak celý seznam.

> **⚠️ Pozor na falešnou úsporu času**
>
> Přidat bez porozumění ignorování do PHPStanu nebo změnit očekávání testu tak, aby „zelenal“, není oprava. Zmizel by důkaz, ne chyba. Stejně tak neodstraňujte test jen proto, že po změně selže.

## 3. Experiment: test nejdřív zachytí regresi

Tento experiment dělejte až po čistém běhu kontrol a jen na vlastní pracovní větvi.

1. Otevřete `app/Model/Product/ProductInputValidator.php` a najděte kontrolu formátu `code`.
2. Záměrně ji na okamžik oslabte tak, aby `špatně` prošlo. Nesahejte na test.
3. Spusťte pouze `composer tester` a poznamenejte si, který příkaz `Assert` selhal a proč.
4. Změnu ve validátoru vraťte ručně v editoru, uložte soubor a znovu spusťte `composer tester`.
5. Před pokračováním ověřte `git diff`: pracovní strom nemá obsahovat záměrně rozbitou validaci.

To je **regrese**: dříve platné chování se nechtěně zhoršilo. Test nezabrání napsání chyby, ale dá rychlou, opakovatelnou zprávu, že se zhoršení stalo.

## 4. Co vyžaduje integrační nebo ruční ověření

Repository, přihlášení, upload a API mají hranice mimo samotný validátor: databázi, session, souborový upload a HTTP. Než pro ně vytvoříte rozsáhlou automatizaci, musíte umět přesně vymezit scénář.

### Závěrečný manuální scénář

Postupujte s jedním výukovým účtem a zapisujte výsledek každého bodu. Neprovádějte mazání na cizí ani produkční databázi.

| Krok | Akce | Očekávaný pozorovatelný výsledek |
|---:|---|---|
| 1 | Otevřete `/product` bez přihlášení | přesměrování na přihlášení a po přihlášení návrat na původní stránku |
| 2 | Přihlaste se špatným heslem | obecná chyba, ne informace, zda e-mail existuje |
| 3 | Filtrujte textem a přejděte na další stranu | query zůstane v URL a počet položek je omezený na stranu |
| 4 | Vytvořte produkt s platnými údaji | flash zpráva, přesměrování, produkt je v seznamu |
| 5 | Zkuste zápornou cenu nebo špatný kód | formulář ukáže validaci a produkt nevznikne |
| 6 | Produkt upravte, poté otevřete neexistující ID | změna se uloží; neexistující záznam vrátí 404 |
| 7 | Smažte testovací produkt | Network ukáže POST, produkt po přesměrování není v seznamu |
| 8 | Importujte platný i chybný vzor | výsledky mají počty a chyby podle řádků; platný řádek z chybného souboru projde |
| 9 | Otevřete API pro existující i chybějící kód | JSON se stavem 200, respektive 404 |

Toto je záměrně **manuální acceptance checklist**, ne unit test. Obsahuje spolupráci prvků, které jednotkový test validátoru nevidí.

## 5. Refactoring: změna tvaru, ne významu

Refactoring upravuje strukturu kódu, ale zachovává pozorovatelné chování. Příklad z tohoto projektu:

```text
nečitelná varianta:
Presenter → načte request → čte CSV → validuje → zapisuje SQL → skládá HTML

současná varianta:
Presenter → přijme request a zvolí odpověď
ProductImporter → čte a vyhodnocuje CSV
ProductInputValidator → zná pravidla hodnot
ProductRepository → čte a zapisuje produkt
Latte → zobrazí připravená data
```

Rozdělení nemá být samoúčelné. Má praktický dopad: validátor lze testovat bez MySQL, importer se nemusí starat o HTTP formulář a API i HTML sdílejí repository. Před refactoringem si vždy napište **kontrakt**, který nesmíte porušit – například „neexistující produkt je 404“ nebo „kód produktu je unikátní“ – a vyberte odpovídající důkaz.

## Samostatný návrhový úkol

Vyberte jedno rozšíření a připravte návrh, nikoli hned velký hotový patch:

- kategorie produktů;
- minimální sklad a upozornění;
- řazení seznamu;
- API seznam produktů;
- změna vlastního hesla;
- export CSV.

V jedné tabulce uveďte:

| Otázka | Vaše odpověď |
|---|---|
| Co se změní v databázi? | tabulka/sloupec/index a důvod |
| Jaká bude URL a metoda? | včetně toho, zda jde o GET nebo změnu dat |
| Kdo data čte či zapisuje? | presenter, služba a repository – každý jednou větou |
| Jak se ověří vstup a oprávnění? | konkrétní pravidlo na serveru |
| Jaký test či manuální důkaz přidáte? | normální případ i jedna chybová hranice |

Pro řazení například nikdy nepředávejte název sloupce od uživatele přímo do `order()`. Navrhněte whitelist povolených hodnot `name`, `price`, `stock` a rozhodněte, kde se přeloží na bezpečný název sloupce.

## Minikvíz

1. Co ověřuje `Assert::same([], $errors)`? **Že tento platný vstup nevrátil žádné chyby validace.**
2. Nahrazuje PHPStan unit test? **Ne. Kontroluje jiný druh vlastností a program nespouští se stejnými vstupy jako test.**
3. Co je regresní chyba? **Dříve fungující požadované chování se po změně poruší.**
4. Je ruční HTTP scénář zbytečný, když test projde? **Ne. Ověřuje spolupráci routeru, session, formuláře, databáze a odpovědi.**
5. Jak poznáte dobrý refactoring? **Pozorovatelný kontrakt zůstane stejný a existuje důkaz, že se nezměnil.**

## Kontrolní body a ověřené zdroje

- [ ] Všechny čtyři automatické kontroly projdou bez skrytých potlačení.
- [ ] Umíte pro test validátoru určit přesně vstup, očekávání a hranici, kterou netestuje.
- [ ] Závěrečný manuální scénář má zapsané výsledky pro úspěšnou i chybovou cestu.
- [ ] Návrh rozšíření obsahuje odpovědnosti, validaci, oprávnění a důkaz.

Pro další práci použijte oficiální [Nette Tester](https://tester.nette.org/en/), [úvod do PHPStan](https://phpstan.org/user-guide/getting-started), [Composer scripts](https://getcomposer.org/doc/articles/scripts.md) a [PHP dokumentaci k výjimkám](https://www.php.net/manual/en/language.exceptions.php). Při změně knihoven vždy ověřte jejich aktuální dokumentaci a výstup konkrétní verze nástroje ve vašem projektu.

## Co jste dokončili

Vytvořili jste databázovou aplikaci v Nette, ale důležitější je opakovatelný postup: nejdřív pochopit problém v PHP, rozdělit odpovědnosti, ověřit nedůvěryhodný vstup a změnu doložit testem nebo reálným requestem. [README](../README.md) zůstává vaším portálem pro spuštění aplikace, mapu lekcí a odkazy na oficiální zdroje.
