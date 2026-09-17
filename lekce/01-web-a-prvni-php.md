# Lekce 01 – Jak funguje web a první PHP

**Čas:** 90 minut  
**Výchozí stav:** po přípravné lekci `0-lekce.md` je k dispozici PHP 8.3, Apache a VS Code.

## Co dnes vytvoříme

V `examples/php/01-product.php` vznikne první serverová stránka s kartou produktu. Student uvidí, že prohlížeč dostane HTML, nikoli zdrojový PHP program.

## Co se naučíme

- popsat cestu `Browser → HTTP → Apache → PHP → HTML`,
- rozlišit URL, request a response,
- použít `echo`, proměnnou, řetězec, integer, float a bool,
- vysvětlit, proč PHP běží na serveru a proč výstup escapujeme.

## Kde jsme skončili

Máme ověřené lokální prostředí. Nette ani databázi zatím nepoužíváme. To je záměr: nejdřív oddělíme jazyk PHP od frameworku.

## Nové pojmy

klient, server, URL, HTTP request, HTTP response, Apache, PHP, HTML, proměnná, typ, `echo`, escaping.

## PHP princip

PHP soubor začíná `<?php`. Příkaz končí středníkem. Proměnná začíná `$` a může obsahovat například `'Notebook'`, `12`, `18990.0` nebo `true`. Operátor `.` spojuje řetězce. Komentář `//` vysvětluje důvod nebo kontext; neopakuje mechanicky stejný text jako kód.

```php
<?php

$name = 'Notebook 15'; // Data produktu budou později pocházet z databáze.
$stock = 12;
echo '<h1>' . htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</h1>';
echo '<p>Sklad: ' . $stock . '</p>';
```

`htmlspecialchars()` není dekorace. Kdyby jméno obsahovalo HTML, bez escapingu bychom mohli omylem vložit cizí značku nebo skript.

## Nette princip

Dnes žádný Nette mechanismus nepřidáváme. Připravujeme si referenční bod, ke kterému se vrátíme v lekci 5: čisté PHP musí samo vybrat soubor, sestavit HTML a poslat odpověď.

## Jak to funguje

```text
prohlížeč -- GET /01-product.php --> Apache
prohlížeč <-- HTTP response: HTML -- PHP vykoná soubor
```

## Postup krok za krokem

1. Otevři `examples/php/01-product.php`. Jde o úplný soubor, ne o vystřižený fragment.
2. Spusť jej přes Apache, například adresou `http://localhost/.../examples/php/01-product.php`, nebo dočasně zkopíruj soubor do veřejného adresáře testovacího projektu.
3. V prohlížeči otevři „Zobrazit zdrojový kód“. Najdi `<h1>` a ověř, že v něm není `<?php`, `$name` ani `echo`.
4. Změň `$stock` a `$active`, obnov stránku a předem odhadni změnu.
5. Vypni Apache a zkus stejnou adresu. Odpověď se nepodaří získat, protože PHP soubor potřebuje serverový výklad.

## Co se právě stalo

Server načetl PHP, nahradil výrazy jejich hodnotami a odeslal výsledek. Prohlížeč už neví, jestli HTML vzniklo v PHP, Pythonu nebo ručně. To je rozdíl mezi zdrojovým kódem a odpovědí.

## Experiment

Do jména produktu vlož text `<strong>Test</strong>`. Porovnej výstup s escapováním a bez něj. Zapiš, která verze zobrazí text doslova a proč je to bezpečnější.

## Miniúkol

Přidej proměnnou `$price` typu `float`, vypiš cenu s desetinnými místy a vytvoř odkaz na API, který zatím může vést na neexistující adresu. Uveď v komentáři, že odkaz začne fungovat až v lekci 14.

## Minikvíz

1. Co dostane prohlížeč: PHP zdroj, nebo výsledné HTML? **Výsledné HTML.**
2. K čemu slouží `$`? **Označuje proměnnou.**
3. Mění `echo` databázi? **Ne, zapisuje výstup do odpovědi.**
4. Proč escapujeme text v HTML? **Aby se data nestala nechtěnou značkou nebo skriptem.**

## Nejčastější chyby

- chybějící `;`,
- záměna `.` za `+` při spojování textu,
- ruční zveřejnění PHP v HTML zdroji,
- neescapování hodnoty z budoucího formuláře,
- otevření souboru přes `file:///` místo přes Apache.

## Kontrolní body

- stránka vrátí HTTP 200,
- zobrazí se kód, název, cena a sklad,
- zdroj stránky neobsahuje PHP,
- změna proměnné se projeví po obnovení stránky.

## Shrnutí

PHP je serverový jazyk. Apache přijme HTTP request, PHP vyrobí HTML a prohlížeč zobrazí response. Data zpracováváme jako nedůvěryhodná a vkládáme je do HTML až po escapingu.

## Co bude příště

Více produktů uložíme do polí a pomocí podmínek a `foreach` z nich sestavíme tabulku.

## Stav projektu po lekci

- Funguje izolovaná karta produktu v čistém PHP.
- Databáze, Nette, formuláře a přihlášení záměrně ještě nefungují, protože je teprve zavedeme.
- Přibyl `examples/php/01-product.php`.

## Poznámka pro učitele

Nejtěžší bývá oddělit PHP od HTML v prohlížeči. Zastavte se u zdrojového kódu a nechte studenty předpovědět, co se stane po změně proměnné. Při nedostatku času lze vynechat detail typů, nesmí se přeskočit request/response a escaping.
