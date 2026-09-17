# Lekce 11 – Editace, validace a bezpečné mazání

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 10](10-nette-forms-pridani.md) · **Výsledek:** editace, 404 a POST mazání.

> **🎯 Cíl lekce**
>
> `/product/edit/4` načte konkrétní produkt do stejného formuláře. Neexistující ID vrací 404 a tlačítko „Odstranit“ odešle POST, který server odmítne přes GET i bez JavaScriptového potvrzení.

## INSERT a UPDATE nejsou stejná operace

Při vytvoření nemáme existující řádek: použijeme INSERT. Při editaci máme ID konkrétního řádku: nejdřív ho načteme, naplníme formulář a po validaci použijeme UPDATE. Platné číslo v URL ale ještě neznamená, že řádek existuje.

```text
GET /product/edit/4 → find(4) → setDefaults() → formulář
POST formuláře      → validace → update(4, data) → redirect
```

## Načtení a 404

**📄 Fragment:** `app/Presentation/Product/ProductPresenter.php`

```php
public function actionEdit(int $id): void
{
	$product = $this->products->find($id);
	if ($product === null) {
		$this->error('Produkt neexistuje.');
	}
	$this->editingId = $id;
	$this['productForm']->setDefaults([
		'code' => (string) $product['code'],
		'name' => (string) $product['name'],
		'stock' => (int) $product['stock'],
		'price' => (float) $product['price'],
	]);
}
```

`int $id` je typový požadavek pro parametr URL. Nette přesto nemůže slíbit, že ID existuje v databázi; proto kontrolujeme `null`. `$this->error()` ukončí zpracování správnou chybovou odpovědí 404.

## Jedna komponenta, dva režimy

`$editingId` je `?int`: buď obsahuje ID upravovaného produktu, nebo `null` znamená vytváření. Úspěšný callback proto rozhoduje mezi `create()` a `update()`.

```php
if ($this->editingId === null) {
	$this->products->create($values);
} else {
	$this->products->update($this->editingId, $values);
}
```

Používáme `===`, protože porovnává hodnotu i typ. Nechceme, aby se `0`, `false`, prázdný text a `null` nerozlišovaly jen kvůli volnějšímu porovnání.

## Proč mazání nikdy není GET

GET request může prohlížeč opakovat, uložit do historie, načíst náhledem nebo vyžádat robot. Proto „kliknutí na odkaz“ nesmí samo o sobě měnit data.

**📄 Skutečný handler:** `ProductPresenter::handleDelete()`

```php
#[Nette\Application\Attributes\Requires(methods: 'POST')]
public function handleDelete(int $id): void
{
	if ($this->products->find($id) === null) {
		$this->error('Produkt neexistuje.');
	}
	$this->products->delete($id);
	$this->flashMessage('Produkt byl odstraněn.', 'success');
	$this->redirect('default');
}
```

`#[Requires(…)]` je PHP attribute – zápis, kterým předáváme Nette metadata k metodě. Zde výslovně požadujeme POST. Nette pro tyto citlivé signály chrání i same-origin požadavek; nepřijímá změnu jen proto, že ji někdo zkusil vyvolat jako běžný GET odkaz.

## Formulář mazání a role JavaScriptu

**📄 Fragment:** `app/Presentation/Product/default.latte`

```latte
<form class="inline-form" method="post" action="{link delete! $product->id}" data-confirm="Opravdu odstranit {$product->name|escapeJs}?">
	<button class="link-button danger" type="submit">Odstranit</button>
</form>
```

`method="post"` je bezpečnostně podstatná část. `data-confirm` jen nese text pro JavaScript. V `www/assets/app.js` volá prohlížeč `window.confirm`; když je JavaScript vypnutý, formulář stále odešle POST a serverová ochrana dál platí.

> **⚠️ Pozor**
>
> `confirm()` není autentizace, autorizace ani CSRF ochrana. Je to UX: pomáhá člověku nekliknout omylem. Bezpečnost kontroluje server přes metodu, původ requestu a přihlášení.

## Postup krok za krokem

1. Přihlaste se a z adresy seznamu otevřete editaci existujícího produktu. Ověřte, že formulář obsahuje jeho skutečné hodnoty.
2. Změňte jen popis a uložte. Po redirectu se změna zobrazí v seznamu nebo po návratu na editaci.
3. Otevřete URL s neexistujícím vysokým ID, například `/product/edit/999999`. Musíte dostat 404, nikoli prázdný formulář a ne 500.
4. V repository najděte `update(int $id, array $data)`. Ověřte, že obsahuje `where('id', $id)`: bez něj by mohl UPDATE změnit více řádků.
5. Vytvořte lokální testovací produkt s jedinečným kódem. Klikněte na Odstranit, nejprve potvrzení zrušte, potom potvrďte.
6. Ověřte, že produkt po POST zmizel. Otevřete stejný mazací URL parametr v prohlížeči jako GET; server změnu neprovede.
7. Vypněte JavaScript pouze pro lokální stránku a test zopakujte s dalším testovacím produktem. POST funguje; ochrana nestojí na dialogu.

## Experiment: bezpečnostní tabulka

Vyplňte čtyři řádky:

| Situace | Co zkusí prohlížeč | Kde je ochrana | Co ochrana nedokazuje |
|---|---|---|---|
| kliknutí na Odstranit | POST formulář | `Requires(methods: 'POST')` | zda má uživatel právo mazat bez loginu |
| zrušení dialogu | žádný submit | `confirm()` UX | není bezpečnostní bariéra |
| GET mazací URL | GET | server odmítne metodu | neřeší jiné chyby formuláře |
| ID neexistuje | `find()` vrátí null | 404 | že uživatel je přihlášen |

## Samostatný úkol

Přidejte k editaci malý nadpis obsahující kód právě upravovaného produktu. Text bezpečně vypište v Latte. Zapište, proč je zobrazení kódu v nadpisu výstupní problém, zatímco kontrola existence produktu patří do presenteru/repository.

## Minikvíz

1. Co dělá `setDefaults()`? **Naplní formulář počátečními hodnotami.**
2. Je `confirm()` bezpečnostní opatření? **Ne, je to UX.**
3. Proč GET nemaže data? **Může být opakován nebo vyžádán bez vědomého potvrzení změny.**
4. Co vracíme pro neexistující editované ID? **HTTP 404.**

## Kontrolní body a zdroje

- [ ] Existující produkt jde upravit.
- [ ] Neexistující produkt vrací 404.
- [ ] Mazání vyžaduje POST a po něm následuje redirect.
- [ ] Vypnutí JavaScriptu nevypne serverové omezení metody.

Použijte [Nette `Requires` attribute](https://doc.nette.org/en/best-practices/attribute-requires), [POST odkazy](https://doc.nette.org/en/best-practices/post-links), [HTTP metody na MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Methods) a [HTTP 404](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/404).

## Stav projektu po lekci

Máme vytvoření, editaci, 404 pro chybné ID a bezpečné POST mazání s potvrzením. Administrace stále není chráněná přihlášením – další lekce přidá session a hashovaná hesla.

**Příště:** přihlášení ověří e-mail a heslo bez ukládání hesla v otevřené podobě.
