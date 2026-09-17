# Lekce 05 – První aplikace v Nette

**Čas:** 90 minut  
**Výchozí stav:** Composer web-project a ukázková třída produktu.

## Co dnes vytvoříme

Vlastní domovskou stránku v `app/Presentation/Home/HomePresenter.php` a `default.latte`. Aplikaci spustíme přes `www/index.php`.

## Co se naučíme

- přiřadit adresářům `www/`, `app/`, `config/`, `temp/`, `log/` a `vendor/` odpovědnost,
- vysvětlit úlohu Bootstrapu a DI containeru,
- popsat cestu requestu až k HTML response,
- rozlišit Nette mechanismus od běžné PHP třídy.

## Kde jsme skončili

Composer umí načíst balíčky. Nette web-project je nainstalovaný a jeho veřejným kořenem je `www/`.

## Nové pojmy

web-project, Bootstrap, DI container, router, presenter, template, veřejný adresář, Tracy.

## PHP princip

`HomePresenter` je stále běžná PHP třída. `extends Nette\Application\UI\Presenter` znamená, že přebírá schopnosti základní třídy. PHP samo ale neví, že má třídu použít na URL; to zařídí Nette Application.

## Nette princip

Nette Application zpracuje request přes presenter. `app/Bootstrap.php` zapne autoloading, Tracy a konfigurační soubory. `RouterFactory` rozhodne, který presenter a action odpovídá URL.

## Jak to funguje

```text
browser → www/index.php → Bootstrap → DI container → Router
       → HomePresenter → Latte → HTML response → browser
```

## Postup krok za krokem

1. Otevři `www/index.php`. Komentář v lekci doplň tak, aby vysvětlil, proč je `www/` jediný veřejný adresář.
2. V `app/Presentation/Home/HomePresenter.php` ověř namespace a dědičnost. Do metody nic nepřidávej, pokud není potřeba.
3. V `app/Presentation/Home/default.latte` změň nadpis. Latte proměnné a značky se zpracují na serveru.
4. V `app/Core/RouterFactory.php` zkontroluj fallback route `Home:default`. Otevři `http://localhost/…/www/` a ověř HTTP 200.
5. Záměrně smaž jednu složenou závorku v presenteru, obnov stránku a přečti Tracy red screen. Chybu vrať zpět.

## Co se právě stalo

Bootstrap nepředává data přímo do HTML. Vytvoří služby, router vybere presenter a presenter vybere template. Cache v `temp/` a logy v `log/` nejsou určeny k publikování.

## Experiment

Přidej do Home presenteru jednoduchou vlastnost nebo metodu a vypiš ji do Latte. Vysvětli, kdo vytvořil presenter a proč se nepíše `new HomePresenter` do `www/index.php`.

## Miniúkol

Do úvodní stránky přidej tři karty „PHP“, „Nette“ a „MySQL“. Karty patří do Latte, nikoli do Bootstrapu.

## Minikvíz

1. Který adresář je veřejný? **`www/`.**
2. Kdo vybírá presenter? **Router.**
3. Co obsahuje `temp/`? **Cache a dočasné soubory.**
4. Je presenter speciální jazyk? **Ne, je to PHP třída s konvencí Nette.**

## Nejčastější chyby

- nastavení document root na celý projekt místo na `www/`,
- přímé zpřístupnění `config/` nebo `app/`,
- chybějící namespace,
- úprava vygenerovaného souboru bez pochopení toku,
- ignorování Tracy místo čtení první chybové řádky.

## Kontrolní body

- homepage vrátí HTTP 200,
- změna v Latte se projeví,
- student nakreslí tok requestu,
- citlivé adresáře nejsou součástí veřejné URL.

## Shrnutí

Nette organizuje běžné PHP do jasného toku. Bootstrap připraví prostředí, DI container služby, router odpovědnost a presenter odpověď.

## Co bude příště

Do presenteru přidáme routing, akce, layout a Latte výpis dočasných produktů.

## Stav projektu po lekci

- Funguje vlastní Nette homepage a Tracy diagnostika.
- Produkty ještě nejsou v databázi ani v chráněné administraci.
- Přibyly `app/Bootstrap.php`, `app/Core/RouterFactory.php`, Home presenter a layout.

## Poznámka pro učitele

Nechte studenty sledovat cestu jednoho requestu, nikoli detail každé interní třídy. Je-li málo času, lze vynechat konfiguraci Tracy, ale nesmí se přeskočit role `www/` a `Router → Presenter → Latte`.
