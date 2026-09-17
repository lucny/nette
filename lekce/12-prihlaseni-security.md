# Lekce 12 – Přihlášení a Nette Security

**Čas:** 2 × 45 minut  
**Výchozí stav:** produktová administrace bez přístupu.

## Co dnes vytvoříme

Tabulku `user`, `UserRepository`, vlastní `Authenticator`, login form, logout a ochranu produktových presenterů. Účet vytvoříme CLI příkazem `bin/create-user.php`.

## Co se naučíme

- odlišit identifikaci, autentizaci a autorizaci,
- použít `interface`, `implements` a výjimku,
- bezpečně hashovat heslo přes Nette `Passwords`,
- vysvětlit session a proč nevytvářet veřejnou stránku pro admina.

## Kde jsme skončili

Kdokoli, kdo zná URL, může otevřít CRUD. To je bezpečnostní chyba, nikoli jen chybějící tlačítko.

## Nové pojmy

identity, autentizace, autorizace, session, authenticator, hash, výjimka, interface.

## PHP princip

Interface je kontrakt: třída, která jej `implements`, musí dodat požadovanou metodu. `try/catch` zachytí očekávané selhání přihlášení a zobrazí obecnou zprávu.

## Nette princip

Nette `User` drží stav přihlášení a zavolá `Authenticator`. Identity obsahuje ID a role. Heslo se ověří proti hashi funkcí `Passwords::verify`; otevřené heslo se do databáze nikdy neuloží.

## Jak to funguje

```text
email + heslo → User::login → Authenticator → UserRepository → user.password_hash
                         ↓ úspěch
                      session identity → chráněný presenter
```

## Postup krok za krokem

1. Spusť `database/schema.sql`, pokud tabulka `user` neexistuje.
2. Projdi `app/Model/User/Authenticator.php`. Komentář vysvětlí, proč vrací `SimpleIdentity` a proč při chybě nesděluje, zda existuje e-mail.
3. Vytvoř účet příkazem `php bin/create-user.php student@example.test vyukove-heslo`. Výukové heslo není určeno pro skutečný účet.
4. Otevři `/product` bez session. Presenter uloží backlink a přesměruje na `/sign/in`.
5. Přihlas se, zkontroluj cookie/session a otevři seznam. Potom klikni Odhlásit a ověř zneplatnění session.
6. V `services.neon` ověř, že authenticator dostává UserRepository a Passwords přes DI.

## Co se právě stalo

Identifikace říká „kdo tvrdí, že je“, autentizace ověřuje heslo a autorizace rozhodne, co smí. V tomto školním projektu má přihlášený uživatel roli `admin`; administraci uživatelů nebudujeme.

## Experiment

Zkus změnit jeden znak hesla v login formuláři. Ověř, že chyba vypadá stejně jako neexistující e-mail. Tím neprozrazujeme existenci účtů.

## Miniúkol

Přidej do layoutu e-mail přihlášeného uživatele, ale nikdy nevypisuj hash. Vysvětli, proč identity zůstává v session a proč se dá session zneplatnit logoutem.

## Minikvíz

1. Co ukládáme do DB? **Hash hesla, ne heslo.**
2. Co je autentizace? **Ověření identity.**
3. Kdo rozhoduje o přístupu presenteru? **Aplikační kontrola uživatele/role.**
4. Proč není vhodný veřejný create-admin formulář? **Kdokoli by mohl vytvořit privilegovaný účet.**

## Nejčastější chyby

- plaintext heslo v `seed.sql`,
- hash vytvořený vlastním MD5/SHA1 místo password hashe,
- detailní „e-mail neexistuje“ vs. „heslo je špatně“,
- ochrana jen v navigaci,
- zapomenutý logout s aktivní session.

## Kontrolní body

- bez loginu je CRUD nedostupný,
- správné heslo přihlásí,
- nesprávné heslo nic neprozradí,
- databáze obsahuje jen hash.

## Shrnutí

Nette Security poskytuje workflow, ale databázové ověření píšeme sami. Bezpečnost není schování odkazu; server musí zkontrolovat session při každém chráněném požadavku.

## Co bude příště

Přidáme upload CSV, kontrolu hlavičky, validaci řádků, transakci a UPSERT podle unikátního kódu.

## Stav projektu po lekci

- Funguje login, logout a ochrana administrace.
- Hesla se vytvářejí CLI postupem a ukládají se jako hash.
- Přibyly UserRepository, Authenticator, Sign presenter a `bin/create-user.php`.

## Poznámka pro učitele

Nechte studenty opakovat slovní trojici identifikace–autentizace–autorizace. Při nedostatku času vynechte role, nesmí se přeskočit hashování a kontrola serverového přístupu.
