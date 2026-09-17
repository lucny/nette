Ano. Lekci 0 bych pojal jako „přípravu laboratoře“: student zatím nemusí rozumět Apache, PHP ani MySQL do hloubky, ale na konci musí mít ověřeno, že všechny části prostředí skutečně fungují. Součástí lekce je proto i diagnostika typických problémů, nikoli jen instalační postup.

Při přípravě tohoto návrhu 17. 9. 2026 nabízí Laragon jako aktuální plnou verzi 8.7.0; Full distribuce obsahuje mimo jiné Apache 2.4.68, PHP 8.3–8.5 a MySQL 8.4 i novější řady. Pro náš kurz bych ale prostředí pevně sjednotil na **Apache 2.4 + PHP 8.3 + MySQL 8.4 + Composer 2**. ([Laragon][1])

# Lekce 0 – Připravujeme vývojové prostředí

## Co dnes vytvoříme

Než začneme programovat v PHP a frameworku Nette, potřebujeme na počítači prostředí, ve kterém budou naše webové aplikace fungovat.

Na konci této lekce budete mít připraveno:

* webový server Apache,
* PHP 8.3,
* databázový server MySQL 8,
* Composer pro instalaci PHP knihoven,
* lokální adresář pro naše projekty,
* funkční lokální webovou adresu typu `http://test.test`,
* terminál, ze kterého lze spouštět PHP a Composer.

Všechny tyto nástroje budeme spravovat pomocí programu **Laragon**.

---

# 1. Proč potřebujeme Laragon?

Běžná webová stránka napsaná pouze v HTML může být otevřena přímo z disku:

```text
stranka.html
↓
prohlížeč
```

PHP aplikace ale funguje jinak.

PHP kód nejprve zpracuje server:

```text
prohlížeč
    ↓ HTTP požadavek
Apache
    ↓
PHP
    ↓
vygenerované HTML
    ↓ HTTP odpověď
prohlížeč
```

Později přibude také databáze:

```text
                 ┌─────────────┐
                 │    MySQL    │
                 └──────▲──────┘
                        │
prohlížeč → Apache → PHP/Nette
                        │
                        ↓
                 výsledné HTML
```

Na profesionálním serveru jsou Apache, PHP a MySQL obvykle samostatné technologie.

Na našem počítači je však budeme spravovat společně pomocí Laragonu.

Laragon je lokální vývojové prostředí pro Windows. Jednotlivé nástroje udržuje převážně uvnitř vlastní adresářové struktury a umožňuje spouštět a zastavovat webový i databázový server z jednoho místa. ([Laragon][2])

---

# 2. Co budeme v kurzu používat

Pro tento kurz budeme všichni používat stejnou konfiguraci:

| Technologie | Verze              |
| ----------- | ------------------ |
| Windows     | 10 nebo 11, 64 bit |
| Laragon     | aktuální řada 8.x  |
| Apache      | 2.4.x              |
| PHP         | 8.3.x              |
| MySQL       | 8.4.x              |
| Composer    | 2.x                |
| editor      | Visual Studio Code |

Pokud máte v Laragonu také PHP 8.4 nebo PHP 8.5, není to chyba.

V tomto kurzu však budeme používat **PHP 8.3**, aby měli všichni stejné prostředí.

Podobně Laragon může obsahovat MySQL 9.x. Pro kurz zvolíme **MySQL 8.4**.

---

# 3. Než začnete

Zkontrolujte, zda máte:

* 64bitový Windows 10 nebo 11,
* připojení k internetu,
* několik GB volného prostoru,
* právo instalovat programy.

Pokud pracujete na školním počítači, řiďte se pravidly správce učebny.

Nevypínejte kvůli instalaci:

* antivirus,
* Windows Defender,
* firewall,
* jiné bezpečnostní mechanismy.

Pokud systém nějakou operaci nepovolí, obraťte se na učitele.

---

# 4. Stažení Laragonu

Používejte pouze oficiální web Laragonu:

**[https://laragon.org](https://laragon.org)**

Otevřete sekci **Download** a zvolte variantu:

```text
Laragon Full – 64 bit
```

Varianta Full je pro náš kurz nejvhodnější, protože již obsahuje většinu nástrojů, které budeme potřebovat.

Při přípravě tohoto kurzu byla aktuální verze Laragon 8.7.0 Full o velikosti přibližně 229 MB. Obsahovala mimo jiné Apache, několik verzí PHP, MySQL, Node.js a Git. V době, kdy budete kurz používat, může být číslo verze vyšší. ([Laragon][1])

> **Důležité:** nestahujte Laragon z náhodných katalogů programů a webů třetích stran.

---

# 5. Spuštění instalátoru

Spusťte stažený instalační soubor.

Pokud Windows zobrazí standardní bezpečnostní dialog, ověřte, že jste instalátor získali z oficiálního webu.

Pokračujte instalací.

---

# 6. Kam Laragon nainstalovat

Jako instalační adresář ponechte:

```text
C:\laragon
```

Toto umístění budeme používat v celém kurzu.

Nepoužívejte například:

```text
C:\Program Files\Laragon
```

ani:

```text
C:\Users\Jan Novak\OneDrive\Moje PHP projekty\Laragon
```

Jednoduchá cesta:

```text
C:\laragon
```

má několik výhod:

* neobsahuje mezery,
* neobsahuje české znaky,
* není synchronizována OneDrivem,
* snadno se zapisuje do terminálu,
* odpovídá výchozí struktuře Laragonu.

Laragon standardně používá právě `C:\laragon`; projekty ukládá do `C:\laragon\www` a databázová data do `C:\laragon\data`. ([Laragon][3])

---

# 7. Volby instalátoru

Instalátor nabízí několik možností.

## Run Laragon When Windows Starts

Pro školní kurz doporučujeme:

```text
☐ Run Laragon When Windows Starts
```

tedy automatické spouštění vypnout.

Laragon budeme spouštět pouze tehdy, když jej skutečně potřebujeme.

Není to technický požadavek. Pokud používáte Laragon denně na vlastním počítači, můžete automatické spuštění později zapnout.

---

## Auto Virtual Hosts

Tuto možnost naopak zapněte:

```text
☑ Auto Virtual Hosts
```

Je pro nás důležitá.

Umožní například z adresáře:

```text
C:\laragon\www\eshop
```

automaticky vytvořit lokální adresu:

```text
http://eshop.test
```

Nemusíme ručně upravovat konfiguraci Apache ani soubor `hosts`. ([Laragon][4])

Dokončete instalaci.

---

# 8. První spuštění

Spusťte Laragon.

Uvidíte hlavní okno aplikace.

Pro nás budou zatím důležitá zejména tlačítka:

```text
Start All
Stop
Menu
```

Klikněte na:

```text
Start All
```

Laragon spustí potřebné služby.

Pro náš kurz nás budou zajímat především:

```text
Apache
MySQL
```

Apache je webový server.

MySQL je databázový server.

---

# 9. Co znamená „server“ na vlastním počítači?

Slovo server může znamenat:

1. fyzický počítač,
2. program poskytující nějakou službu.

Zde myslíme druhý význam.

Apache je program čekající na HTTP požadavky.

MySQL je program čekající na databázové požadavky.

Oba běží na vašem počítači.

Proto používáme pojem:

**localhost** neboli lokální hostitel.

Vaše aplikace zatím není dostupná z internetu.

---

# 10. Kontrola Apache

Po spuštění Laragonu otevřete prohlížeč a zadejte:

```text
http://localhost
```

Měla by se zobrazit stránka Laragonu nebo jiná výchozí stránka lokálního serveru.

Pokud ano, znamená to:

```text
prohlížeč
    ↓
Apache
    ↓
HTTP odpověď
```

Webový server funguje.

---

# 11. Výběr PHP 8.3

Laragon Full může obsahovat několik verzí PHP.

Otevřete:

```text
Menu
→ PHP
→ Version
```

Vyberte nejnovější dostupnou verzi začínající:

```text
8.3
```

Například:

```text
PHP 8.3.x
```

Konkrétní třetí číslo není důležité.

Poté použijte:

```text
Reload
```

nebo Laragon restartujte.

Laragon umí vedle sebe udržovat více verzí PHP a mezi nimi přepínat prostřednictvím nabídky `PHP → Version`. ([Laragon][5])

---

# 12. Výběr MySQL 8.4

Podobně zkontrolujte databázi.

Otevřete nabídku MySQL a zvolte verzi:

```text
MySQL 8.4.x
```

Pokud je v Laragonu také například:

```text
MySQL 9.x
```

pro tento kurz ji nepoužívejte.

Chceme, aby celá třída používala stejnou hlavní verzi databáze.

Pokud MySQL 8.4 ve vaší instalaci není, Laragon umožňuje další verzi přidat prostřednictvím:

```text
Menu
→ Tools
→ Quick add
→ MySQL ...
```

([Laragon][5])

---

# 13. Proč nemáme používat „prostě nejnovější všechno“?

Při vývoji aplikací záleží na verzích.

Program může například fungovat s:

```text
PHP 8.3
```

ale využívat vlastnost, která ve starší verzi PHP neexistovala.

Proto každý profesionální projekt definuje své požadavky na prostředí.

V našem případě říkáme:

```text
PHP >= 8.3
MySQL 8
```

Pro výuku ještě požadavky zpřesňujeme:

```text
PHP 8.3.x
MySQL 8.4.x
```

Ne proto, že by jiné verze nutně nefungovaly, ale proto, aby výsledky byly co nejlépe reprodukovatelné.

---

# 14. Přidání nástrojů Laragonu do PATH

Nyní chceme, aby příkaz:

```text
php
```

fungoval také v terminálu Visual Studio Code.

Otevřete:

```text
Menu
→ Tools
→ Path
→ Add Laragon to Path
```

Laragon tím zpřístupní své nástroje také ostatním programům Windows. ([Laragon][1])

Pokud už máte otevřený Visual Studio Code, celý jej zavřete a znovu spusťte.

Důvod je jednoduchý: spuštěná aplikace nemusí automaticky zaznamenat právě změněnou proměnnou PATH.

---

# 15. Co je PATH?

Když napíšeme:

```text
php
```

Windows musí zjistit, kde se program `php.exe` nachází.

Mohl by být například zde:

```text
C:\laragon\bin\php\...
```

Proměnná prostředí **PATH** obsahuje seznam adresářů, ve kterých má operační systém program hledat.

Díky tomu nemusíme pokaždé psát celou cestu:

```text
C:\laragon\bin\php\php-8.3...\php.exe
```

Stačí:

```text
php
```

---

# 16. První kontrola z terminálu

Otevřete Visual Studio Code.

Zvolte:

```text
Terminal
→ New Terminal
```

Zadejte:

```bash
php -v
```

Měli byste vidět něco podobného:

```text
PHP 8.3.x ...
```

Důležitý je začátek:

```text
PHP 8.3
```

---

# 17. Odkud se PHP spouští?

Zadejte:

```bat
where php
```

Výsledek by měl ukazovat do adresáře Laragonu.

Například:

```text
C:\laragon\bin\php\...\php.exe
```

Pokud se zde objeví jiné PHP například z XAMPP, WAMPu nebo starší instalace PHP, upozorněte učitele.

Máme-li všichni používat stejné prostředí, musí se skutečně spouštět PHP z Laragonu.

---

# 18. Kontrola Composeru

Nyní zadejte:

```bash
composer --version
```

Měla by se zobrazit verze Composeru 2.x.

Composer je správce PHP balíčků.

Později pomocí něj stáhneme Nette a všechny potřebné knihovny.

Zjednodušeně:

```text
composer.json
     ↓
Composer
     ↓
potřebné knihovny
     ↓
vendor/
```

Nette používá Composer jako standardní způsob instalace balíčků. ([Nette Documentation][6])

Zatím nic neinstalujte.

---

# 19. Kontrola MySQL

Zadejte:

```bash
mysql --version
```

Měla by se zobrazit verze MySQL.

Pro náš kurz očekáváme řadu:

```text
8.4
```

Tento příkaz se ještě nepřipojuje do databáze.

Pouze ověřuje, že je k dispozici klientský program MySQL.

---

# 20. Kontrola důležitých PHP rozšíření

PHP lze rozšiřovat pomocí modulů.

Později budeme například potřebovat modul umožňující připojení k MySQL.

V terminálu spusťte:

```bash
php -r "foreach (['pdo_mysql','mbstring','openssl','intl','fileinfo'] as $e) echo $e . ': ' . (extension_loaded($e) ? 'OK' : 'CHYBI') . PHP_EOL;"
```

Očekáváme:

```text
pdo_mysql: OK
mbstring: OK
openssl: OK
intl: OK
fileinfo: OK
```

Je možné, že některé rozšíření pro první lekce ještě nebudeme potřebovat. Chceme ale prostředí zkontrolovat už nyní.

Pokud některý modul chybí, v Laragonu lze PHP extensions zapínat a vypínat. Změny PHP konfigurace budeme dělat pouze podle pokynu učitele. Laragon tyto operace podporuje přímo ze svého prostředí. ([Laragon][7])

---

# 21. Poznáváme adresáře Laragonu

Otevřete:

```text
C:\laragon
```

Pro nás jsou nejdůležitější tři adresáře:

```text
C:\laragon
│
├── www
├── data
└── usr
```

### `www`

Sem budeme ukládat webové projekty.

```text
C:\laragon\www
```

### `data`

Zde Laragon ukládá data databází.

```text
C:\laragon\data
```

### `usr`

Zde se nachází uživatelská konfigurace Laragonu.

```text
C:\laragon\usr
```

Laragon tyto tři adresáře považuje za hlavní místa obsahující projekty, databázová data a uživatelskou konfiguraci. ([Laragon][3])

---

# 22. Vytvoříme první testovací projekt

V adresáři:

```text
C:\laragon\www
```

vytvořte adresář:

```text
test
```

Výsledkem bude:

```text
C:\laragon\www\test
```

Uvnitř vytvořte soubor:

```text
index.php
```

Celá cesta tedy bude:

```text
C:\laragon\www\test\index.php
```

---

# 23. První PHP program

Do souboru vložte:

```php
<?php

echo '<h1>Vývojové prostředí funguje</h1>';
echo '<p>PHP verze: ' . PHP_VERSION . '</p>';
```

Soubor uložte.

Zatím nemusíte rozumět všem použitým znakům.

Podrobně je rozebereme v další lekci.

Prozatím stačí vědět, že PHP vytvoří část HTML stránky.

---

# 24. Nechte Laragon vytvořit lokální adresu

V Laragonu klikněte na:

```text
Reload
```

Laragon zjistí, že v jeho adresáři `www` vznikl nový projekt.

Protože jsme při instalaci aktivovali Auto Virtual Hosts, vytvoří pro něj lokální adresu:

```text
http://test.test
```

Laragon vytváří podobné adresy automaticky z názvu adresáře projektu. ([Laragon][8])

---

# 25. Otevřete první PHP stránku

Do prohlížeče napište:

```text
http://test.test
```

Měli byste vidět například:

```text
Vývojové prostředí funguje

PHP verze: 8.3.x
```

Právě jste ověřili celý řetězec:

```text
Browser
   ↓
http://test.test
   ↓
Apache
   ↓
index.php
   ↓
PHP 8.3
   ↓
HTML
   ↓
Browser
```

To je velmi důležitý kontrolní bod.

---

# 26. Co se právě stalo?

Prohlížeč **nedostal PHP soubor**.

PHP program:

```php
<?php
echo '<h1>Vývojové prostředí funguje</h1>';
```

spustil server.

Prohlížeč dostal pouze výsledné HTML, například:

```html
<h1>Vývojové prostředí funguje</h1>
<p>PHP verze: 8.3.24</p>
```

To je jeden ze základních principů serverového programování.

---

# 27. Ověřte si rozdíl

V prohlížeči otevřete vývojářské nástroje nebo příkaz:

```text
Zobrazit zdrojový kód stránky
```

Najdete v něm například:

```html
<h1>Vývojové prostředí funguje</h1>
```

Nenajdete:

```php
echo
```

ani:

```php
PHP_VERSION
```

PHP zůstává na serveru.

---

# 28. Volitelně: phpMyAdmin

Později budeme databázi ovládat také pomocí SQL, ale pro začátečníka je užitečné mít možnost její strukturu prohlížet graficky.

Laragon phpMyAdmin ve výchozí instalaci nemusí obsahovat.

Lze jej doplnit:

```text
Menu
→ Tools
→ Quick add
→ phpMyAdmin
```

Pro PHP 8.3 lze použít běžnou podporovanou variantu phpMyAdmin.

Laragon nabízí phpMyAdmin prostřednictvím Quick Add; pro PHP 8.4+ jeho dokumentace doporučuje phpMyAdmin 6. ([Laragon][5])

V této lekci ale ještě žádnou databázi nevytvářejte.

---

# 29. Nette zatím neinstalujeme

Možná vás napadne spustit:

```bash
composer create-project ...
```

Zatím to nedělejte.

Nejdříve se v následujících lekcích seznámíme se základy PHP.

Teprve potom si ukážeme, co za nás framework Nette řeší.

Je důležité nejprve vidět rozdíl mezi:

```text
PHP
```

a:

```text
PHP + Nette
```

---

# 30. Diagnostika prostředí

Na konci lekce musí fungovat následující příkazy:

```bash
php -v
```

```bash
composer --version
```

```bash
mysql --version
```

```bat
where php
```

A v prohlížeči:

```text
http://test.test
```

Pokud všechny testy projdou, prostředí je připraveno.

---

# 31. Co když `php` není rozpoznán?

Pokud terminál vypíše například:

```text
'php' is not recognized ...
```

zkontrolujte:

```text
Laragon
→ Menu
→ Tools
→ Path
→ Add Laragon to Path
```

Potom:

1. zavřete Visual Studio Code,
2. znovu jej spusťte,
3. otevřete nový terminál,
4. spusťte:

```bash
php -v
```

Pokud stále nefunguje, použijte zatím Laragon Terminal:

```text
Menu
→ Laragon
→ Terminal
```

Laragon Terminal používá vlastní izolované prostředí, ve kterém jsou jeho nástroje dostupné automaticky. ([Laragon][9])

---

# 32. Co když se spouští jiné PHP?

Spusťte:

```bat
where php
```

Pokud uvidíte například:

```text
C:\xampp\php\php.exe
```

nepoužíváte PHP z Laragonu.

Na počítači pravděpodobně existuje ještě jiný vývojový stack.

Nemažte jej bez rozmyslu.

Informujte učitele a upravte pořadí PATH tak, aby kurz používal PHP z Laragonu.

---

# 33. Co když Apache nejde spustit?

Webové servery obvykle používají port:

```text
80
```

a HTTPS:

```text
443
```

Pokud již tento port používá jiný program, Apache se nemusí spustit.

Zkontrolovat port 80 můžeme příkazem:

```bat
netstat -ano | findstr :80
```

Port 443:

```bat
netstat -ano | findstr :443
```

Častou příčinou může být:

* jiný Apache,
* IIS,
* XAMPP,
* WAMP,
* jiný lokální server.

Nezačínejte náhodně měnit konfiguraci Apache.

Nejdříve zjistěte, který program port používá.

Laragon umožňuje přímo zobrazovat Apache logy prostřednictvím svého menu, což je při podobných problémech první vhodné místo pro diagnostiku. ([Laragon][10])

---

# 34. Co když nejde spustit MySQL?

MySQL standardně používá port:

```text
3306
```

Zkontrolujte jej:

```bat
netstat -ano | findstr :3306
```

Pokud jej již používá jiný MySQL nebo MariaDB server, může vzniknout konflikt.

Opět nejprve zjistěte příčinu.

Neměňte bez pokynu učitele náhodně porty nebo databázové soubory.

---

# 35. Co když `test.test` nefunguje?

Nejprve zkontrolujte:

1. běží Laragon?
2. běží Apache?
3. existuje adresář:

```text
C:\laragon\www\test
```

4. existuje:

```text
C:\laragon\www\test\index.php
```

5. klikli jste na:

```text
Reload
```

6. je zapnuto:

```text
Auto Virtual Hosts
```

Pokud Laragon nemůže aktualizovat potřebnou konfiguraci systému, může Windows vyžadovat zvýšené oprávnění. V takovém případě postupujte podle pokynu učitele; nevypínejte bezpečnostní mechanismy.

---

# 36. Co když Windows zobrazí firewall?

Při prvním spuštění Apache se může zobrazit dialog Windows Firewall.

Pro vývoj na vlastním počítači není důvod otevírat Apache do veřejných sítí.

Pokud je potřeba síťový přístup, používejte pouze nastavení určené školou nebo učitelem.

Naše aplikace budou běžně používány pouze lokálně:

```text
localhost
*.test
```

---

# 37. Co budeme dělat při každém programování?

Od této chvíle bude typický začátek práce velmi jednoduchý.

### 1. Spustíme Laragon

### 2. Klikneme na

```text
Start All
```

### 3. Spustíme Visual Studio Code

### 4. Otevřeme projekt

například:

```text
C:\laragon\www\products
```

### 5. Pracujeme

Po skončení můžeme služby zastavit:

```text
Stop
```

---

# 38. Co zatím nemusíte řešit

Laragon obsahuje mnohem více nástrojů.

Můžete zde zahlédnout například:

* Nginx,
* Node.js,
* npm,
* PostgreSQL,
* Redis,
* Python.

Pro náš projekt je nyní ignorujte.

Potřebujeme pouze:

```text
Apache
PHP
MySQL
Composer
```

Jednou ze základních schopností programátora je také poznat, co **zatím nepotřebuje**.

---

# 39. Experiment

Otevřete:

```text
C:\laragon\www\test\index.php
```

změňte program na:

```php
<?php

$name = 'Student';

echo '<h1>Ahoj ' . $name . '!</h1>';
echo '<p>Server používá PHP ' . PHP_VERSION . '</p>';
```

Uložte soubor a obnovte:

```text
http://test.test
```

Otázky:

1. Museli jste restartovat Apache?
2. Museli jste PHP znovu kompilovat?
3. Projeví se změna po obnovení stránky?
4. Je ve zdrojovém HTML stránky vidět proměnná `$name`?

Odpovědi zatím nemusíte formulovat technicky přesně. K těmto otázkám se vrátíme v další lekci.

---

# 40. Miniúkol

Upravte stránku tak, aby zobrazovala:

* vaše jméno,
* text „Vývojové prostředí je připraveno“,
* aktuální verzi PHP pomocí `PHP_VERSION`.

Například:

```text
Jan Novák
Vývojové prostředí je připraveno.
PHP 8.3.x
```

Nepište číslo verze PHP ručně.

Použijte:

```php
PHP_VERSION
```

---

# 41. Minikvíz

### 1. Který program v našem prostředí funguje jako webový server?

A) MySQL
B) Apache
C) Composer
D) PHP_VERSION

Správně: **B**

---

### 2. K čemu slouží MySQL?

A) k editaci PHP
B) k zobrazení HTML
C) jako databázový server
D) jako webový prohlížeč

Správně: **C**

---

### 3. Co dělá PHP?

A) pouze ukládá databázi
B) vykonává serverový PHP kód
C) nahrazuje webový prohlížeč
D) je CSS framework

Správně: **B**

---

### 4. Co znamená PATH?

A) seznam databází
B) seznam adresářů, ve kterých systém hledá programy
C) adresa webové stránky
D) PHP proměnná

Správně: **B**

---

### 5. Který adresář budeme používat pro webové projekty?

A)

```text
C:\laragon\data
```

B)

```text
C:\laragon\www
```

C)

```text
C:\Windows
```

D)

```text
C:\laragon\usr
```

Správně: **B**

---

### 6. Dostane prohlížeč zdrojový PHP kód?

A) ano
B) ne

Správně: **B**

PHP kód je vykonán na serveru. Prohlížeč dostává výslednou HTTP odpověď, typicky HTML.

---

# 42. Kontrolní body

Než budete pokračovat další lekcí, musí platit:

```text
[ ] Laragon je nainstalován v C:\laragon

[ ] Apache lze spustit

[ ] MySQL lze spustit

[ ] je vybráno PHP 8.3.x

[ ] je vybráno MySQL 8.4.x

[ ] příkaz php -v funguje

[ ] příkaz composer --version funguje

[ ] příkaz mysql --version funguje

[ ] where php ukazuje PHP z Laragonu

[ ] http://localhost funguje

[ ] http://test.test funguje

[ ] testovací PHP stránka zobrazí verzi PHP
```

Pokud některý z bodů neplatí, vyřešte jej před další lekcí.

---

# 43. Co jsme se naučili

Dnes jsme zatím neprogramovali skutečnou aplikaci.

Připravili jsme prostředí, ve kterém ji budeme vytvářet.

Základní vztah je:

```text
Browser
   ↓
Apache
   ↓
PHP
   ↓
HTML
   ↓
Browser
```

Později jej rozšíříme:

```text
Browser
   ↓
Apache
   ↓
PHP + Nette
   ↓
MySQL
   ↓
PHP + Nette
   ↓
HTML
   ↓
Browser
```

Composer nám později umožní přidat Nette a další PHP knihovny.

---

# 44. Stav prostředí po lekci 0

Na počítači máme připraveno:

```text
Windows
│
├── Visual Studio Code
│
└── Laragon
    │
    ├── Apache 2.4
    ├── PHP 8.3
    ├── MySQL 8.4
    └── Composer 2
```

Projekty budeme vytvářet v:

```text
C:\laragon\www
```

Testovací projekt:

```text
C:\laragon\www\test
└── index.php
```

je dostupný jako:

```text
http://test.test
```

---

# 45. Příště

V další lekci se už zaměříme na samotné PHP.

Zjistíme:

* co znamená `<?php`,
* jak PHP vykonává příkazy,
* proč proměnné začínají `$`,
* co jsou řetězce a čísla,
* co znamená `echo`,
* jak PHP vytvoří výsledné HTML.

Tentokrát již nebudeme připravovat nástroje.

Začneme programovat.

---
