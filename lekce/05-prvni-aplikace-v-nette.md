# Lekce 05 – První aplikace v Nette

**Čas:** 90 minut · **Navazuje na:** [lekci 04](04-objekty-composer.md) · **Výsledek:** vlastní stránka přes Nette.

> **🎯 Cíl lekce**
>
> Na papír i ve skutečném projektu projdete cestu jednoho requestu od `www/index.php` k Latte šabloně. Dokážete vysvětlit, proč je z webu dostupný jen adresář `www/`.

## Co se změní oproti čistému PHP

V lekci 1 jeden soubor současně držel data, rozhodoval a psal HTML. To fungovalo, ale při více stránkách by se kód opakoval. Nette rozdělí práci do spolupracujících částí. Neznamená to, že PHP přestalo platit: presenter je pořád PHP třída, Latte vytvoří HTML a Composer pořád načítá třídy.

```text
prohlížeč
   ↓ HTTP request
www/index.php → Bootstrap → DI container → router → presenter → Latte
                                                              ↓
                                                     HTML response
```

> **🧠 Nejdřív přemýšlej**
>
> Proč by `config/local.neon` neměl být dostupný jako adresa `https://web/config/local.neon`? Který adresář tedy smí být document rootem?

## Mapa projektu

| Místo | Odpovědnost | Proč zde nic veřejně neotvíráme / otvíráme |
|---|---|---|
| `www/` | vstupní skript, CSS, JavaScript | jediný veřejný adresář |
| `app/` | PHP třídy aplikace | obsahuje logiku, ne soubory ke stažení |
| `config/` | nastavení služeb a databáze | může obsahovat citlivé údaje |
| `temp/` | cache | lze kdykoli znovu vytvořit |
| `log/` | diagnostické záznamy | nesmí být veřejné |
| `vendor/` | knihovny z Composeru | není veřejná dokumentace ani upload |

> **⚠️ Pozor**
>
> Nastavit Apache na kořen repozitáře místo na `www/` není drobná chyba vzhledu. Zpřístupnili byste adresáře s konfigurací a zdrojovým kódem.

## Co je PHP a co přidává Nette

| PHP | Nette |
|---|---|
| třída, metoda, `namespace`, `use`, `new` | Bootstrap složí konfiguraci a container |
| dědičnost přes `extends` | router vybere presenter podle URL |
| návratová hodnota a volání metod | presenter najde Latte šablonu a odešle odpověď |

## Dva přesné soubory na začátku toku

**📄 Úplný soubor:** `www/index.php`

```php
<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$bootstrap = new App\Bootstrap;
$container = $bootstrap->bootWebApplication();
$application = $container->getByType(Nette\Application\Application::class);
$application->run();
```

`__DIR__` je adresář právě běžícího souboru. `../vendor/autoload.php` proto najde autoloader o úroveň výš. `$container` je výsledný DI container; teprve z něj si vstupní skript vyžádá `Application` a spustí ji. Tento soubor schválně není místo pro SQL ani HTML – jen spustí aplikaci.

**📄 Fragment:** `app/Bootstrap.php`

```php
public function bootWebApplication(): Nette\DI\Container
{
	$this->initializeEnvironment();
	$this->setupContainer();
	return $this->configurator->createContainer();
}
```

`Nette\DI\Container` je objekt, který zná služby aplikace a dokáže je sestavit. V lekci 8 uvidíte konkrétní příklad: vytvoří `Explorer` a předá ho repository. Nyní stačí rozumět pořadí: nejprve konfigurace, potom container, potom zpracování requestu.

## První presenter a šablona

**📄 Úplný soubor:** `app/Presentation/Home/HomePresenter.php`

```php
<?php declare(strict_types=1);

namespace App\Presentation\Home;

use Nette;

final class HomePresenter extends Nette\Application\UI\Presenter
{
}
```

Třída je zatím prázdná. Přesto umí obsloužit domovskou stránku, protože dědí schopnosti `Presenter` a Nette podle konvencí najde šablonu `app/Presentation/Home/default.latte`.

**📄 Přesný výukový fragment z:** `app/Presentation/Home/default.latte`

```latte
{block content}
<section class="hero">
	<p class="eyebrow">Školní projekt v Nette Frameworku</p>
	<h1 n:block=title>Správa produktů</h1>
	<p>Malá administrační aplikace, na které si vyzkoušíme PHP, HTTP, databázi i bezpečné formuláře.</p>
	<div class="actions">
		<a class="button" n:href="Product:default">Prohlédnout produkty</a>
		<a class="button secondary" n:href="Sign:in">Přihlášení</a>
	</div>
</section>
```

`{block content}` říká, kam se obsah vloží do společného layoutu. `n:href` nevyrábí URL slepováním textu; požádá Nette, aby vytvořilo odkaz na presenter a akci.

## Postup krok za krokem

1. V kořeni projektu spusťte `composer install`, pokud ještě neexistuje `vendor/`.
2. Nastavte document root na `www/`. V Laragonu to znamená, že virtuální host míří na tuto složku, ne na celý repozitář.
3. Otevřete `www/index.php` a sledujte tři kroky: `require` autoloaderu, vytvoření Bootstrapu, spuštění `Application`.
4. Otevřete `app/Bootstrap.php`. Najděte řádek s `setTempDirectory()` a vysvětlete, proč cache nepatří do `app/`.
5. V `setupContainer()` najděte přidání `common.neon`, nepovinného `local.neon` a `services.neon`. Lokální soubor se načte jen, pokud existuje.
6. V `HomePresenter.php` ověřte namespace. Adresář `Home` a namespace jsou součástí dohody, podle níž Nette třídu najde.
7. Změňte v `default.latte` text nadpisu, obnovte stránku a znovu jej vraťte. Změna šablony se projeví jako nová HTTP odpověď.
8. Při chybě se podívejte na první konkrétní řádek Tracy. Nezkoušejte náhodně mazat cache nebo měnit několik souborů naráz.

## Ověřitelný experiment: kudy request prošel

Do šablony doplňte dočasný text `Tato stránka vznikla přes Latte.` Pak:

1. otevřete domovskou stránku;
2. zobrazte zdrojový kód stránky;
3. najděte výsledný HTML text, ale ne zápis `{block content}` ani `n:href`;
4. změnu vraťte.

Tím jste nezjistili jen to, že „Nette funguje“. Doložili jste, že Latte se na serveru přeložilo na HTML stejně, jako se v lekci 1 vykonalo PHP.

## Samostatný úkol

Vytvořte v `default.latte` tři jednoduché karty: PHP, Nette a MySQL. Každá má jeden krátký popis. Rozhodněte, proč patří do šablony, nikoli do `Bootstrap.php`. Před implementací napište jednu větu: „Bootstrap má odpovědnost za …, Latte má odpovědnost za …“.

## Minikvíz

1. Která složka je document root? **`www/`.**
2. Kdo rozhoduje, jaký presenter obslouží URL? **Router.**
3. Je `HomePresenter` samostatný nový jazyk? **Ne, je to PHP třída.**
4. Kam patří lokální heslo databáze? **Do ignorovaného `config/local.neon`, ne do Gitu.**

## Kontrolní body a zdroje

- [ ] Domovská stránka vrací HTTP 200.
- [ ] Změna Latte šablony se projeví v HTML odpovědi.
- [ ] Dokážete nakreslit celý tok requestu.
- [ ] Z webu není přístupná konfigurace ani `app/`.

Čtěte [jak funguje aplikace Nette](https://doc.nette.org/en/application/how-it-works), [strukturu adresářů](https://doc.nette.org/en/application/directory-structure) a [konfiguraci aplikace](https://doc.nette.org/en/application/configuration).

## Stav projektu po lekci

Funguje vlastní domovská stránka, společný layout a diagnostika Tracy. Produkty zatím nemají databázi ani formuláře, ale request už prochází skutečnou strukturou Nette.

**Příště:** vytvoříme produktovou stránku, odkazy přes router a šablonu, která opakuje řádky dat.
