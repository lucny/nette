# Lekce 12 – Přihlášení a Nette Security

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 11](11-editace-validace-mazani.md) · **Výsledek:** chráněná administrace se session.

> **🎯 Cíl lekce**
>
> Nepřihlášený návštěvník se na `/product` nedostane. Výukový účet vytvoříte CLI skriptem, databáze obsahuje pouze hash hesla a po logoutu chráněná stránka opět vyžaduje přihlášení.

## Tři podobná, ale odlišná slova

| Pojem | Otázka | Příklad v aplikaci |
|---|---|---|
| identifikace | „Za koho se vydáváš?“ | e-mail `student@example.test` |
| autentizace | „Dokážeš to?“ | ověření hesla proti hashi |
| autorizace | „Co smíš?“ | přihlášený uživatel smí do administrace |

Schovat tlačítko není autorizace. Server musí přístup zkontrolovat pro každý chráněný request.

> **🧠 Nejdřív přemýšlej**
>
> Kdyby útočník znal přímou adresu `/product`, pomohlo by, že v navigaci nevidí odkaz? Kde musí být skutečná kontrola?

## Heslo se neukládá

```text
uživatel zadá heslo
        ↓
Nette Passwords vytvoří jednosměrný hash
        ↓
databáze uloží password_hash
        ↓
při loginu verify(otevřené heslo, uložený hash)
```

Hash není šifrované heslo, které bychom chtěli dešifrovat. Je to jednosměrný otisk určený k bezpečnému ověření. Sůl a parametry algoritmu jsou součástí výsledného hashe; do tabulky proto máme `VARCHAR(255)`.

**📄 Vytvoření výukového účtu:** `bin/create-user.php`

```php
$database->table('user')->insert([
	'email' => $email,
	'password_hash' => $passwords->hash($password),
	'created_at' => new DateTimeImmutable,
]);
```

Skript je mimo `www/`, proto jej nelze vyvolat přes prohlížeč jako veřejnou stránku „vytvoř admina“.

> **⚠️ Pozor**
>
> `student@example.test` a `vyukove-heslo` jsou veřejné výukové údaje. Použijte je jen lokálně. Pro skutečné systémy nikdy nevkládejte heslo do seed SQL ani do dokumentace.

## Interface a authenticator

**📄 Přesná citace třídy z:** `app/Model/User/Authenticator.php`

```php
final class Authenticator implements AuthenticatorInterface
{
	public function __construct(
		private UserRepository $users,
		private Passwords $passwords,
	) {
	}

	public function authenticate(string $user, string $password): SimpleIdentity
	{
		$row = $this->users->findByEmail($user);
		if ($row === null || !$this->passwords->verify($password, (string) $row['password_hash'])) {
			throw new AuthenticationException('Neplatný e-mail nebo heslo.');
		}

		return new SimpleIdentity((int) $row['id'], ['admin'], ['email' => (string) $row['email']]);
	}
}
```

`implements AuthenticatorInterface` je PHP kontrakt: třída slibuje, že nabídne metodu `authenticate`. `throw` vyvolá očekávaný chybový stav. Login presenter jej zachytí přes `try/catch` a zobrazí jednu obecnou zprávu, aby neprozradil, zda existuje e-mail nebo je špatné heslo.

## Ochrana presenteru

**📄 Fragment:** `ProductPresenter::startup()`

```php
protected function startup(): void
{
	parent::startup();
	if (!$this->getUser()->isLoggedIn()) {
		$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
	}
}
```

`protected` dovoluje frameworku a potomkům používat metodu, ale ne libovolnému kódu zvenku. `parent::startup()` zachová životní cyklus třídy, z níž dědíme. `storeRequest()` uloží původní adresu, aby se po loginu mohl uživatel vrátit tam, kam mířil.

## Login callback a session

**📄 Fragment:** `app/Presentation/Sign/SignPresenter.php`

```php
try {
	$this->getUser()->login($data->email, $data->password);
} catch (AuthenticationException) {
	$form->addError('Neplatný e-mail nebo heslo.');
	return;
}
```

Nette `User` předá údaje authenticatoru. Po úspěchu uloží identitu do session podle konfigurace. Prohlížeč nese identifikátor session v cookie; samotné heslo se do cookie neukládá.

## Postup krok za krokem

1. Ověřte, že existuje tabulka `user` z lekce 7.
2. Spusťte přesně tento lokální příkaz:

```bash
php bin/create-user.php student@example.test vyukove-heslo
```

3. V MySQL si zobrazte `email` a `password_hash`. Hash nesmí vypadat jako zadané heslo.
4. Otevřete `/product` v anonymním okně prohlížeče. Musíte být přesměrováni na `/sign`.
5. Přihlaste se správným heslem. Ověřte návrat na původní stránku a dostupnost administrace.
6. Přihlaste se s chybným heslem a s neexistujícím e-mailem. Zpráva musí být stejně obecná.
7. Klikněte Odhlásit, zavřete stránku produktu a znovu ji otevřete. Bez nové session musí být chráněná.

## Experiment: co prozrazuje chybová zpráva

Vytvořte si dvě situace: správný e-mail + špatné heslo a neexistující e-mail + libovolné heslo. Porovnejte odpovědi. Pak vysvětlete, proč zpráva „e-mail neexistuje“ může útočníkovi usnadnit zjišťování účtů.

## Samostatný úkol

Do layoutu přidejte zobrazení e-mailu přihlášeného uživatele a odkaz na logout. Nikdy nevypisujte `password_hash`. Napište, proč e-mail může být součástí identity v session, ale hash hesla ne.

## Minikvíz

1. Co ukládáme do databáze? **Hash hesla, ne heslo.**
2. Co je autentizace? **Ověření identity.**
3. Kde chráníme `/product`? **Na serveru ve startupu presenteru.**
4. Proč nevytvořit veřejný formulář pro admina? **Kdokoli by mohl vytvořit privilegovaný účet.**

## Kontrolní body a zdroje

- [ ] Anonymní request na CRUD přesměruje na login.
- [ ] Správné heslo přihlásí, špatné nic neprozradí.
- [ ] Tabulka obsahuje hash, ne plaintext.
- [ ] Logout zruší přístup k chráněné stránce.

Čtěte [Nette autentizaci](https://doc.nette.org/en/security/authentication), [Nette Passwords](https://doc.nette.org/en/security/passwords), [PHP password hashing](https://www.php.net/passwords) a [konfiguraci Nette Security](https://doc.nette.org/en/security/configuration).

## Stav projektu po lekci

CRUD produktů je chráněný session a hesla jsou jednosměrně hashovaná. Import CSV zatím nepřijímáme; další lekce přidá nový nedůvěryhodný vstup a samostatnou službu pro jeho zpracování.

**Příště:** upload projde od kontroly souboru přes hlavičku CSV a validaci řádků až k transakčnímu UPSERTu.
