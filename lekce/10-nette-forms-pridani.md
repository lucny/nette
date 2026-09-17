# Lekce 10 – Nette Forms a přidání produktu

**Čas:** 2 × 45 minut · **Navazuje na:** [lekci 09](09-seznam-filtrovani-strankovani.md) · **Výsledek:** validovaný formulář vytvoří produkt.

> **🎯 Cíl lekce**
>
> Stránka `/product/create` přijme POST, zobrazí chyby pro neplatná data, vytvoří validní produkt a po uložení přesměruje na seznam, aby obnovení stránky neprovedlo druhý INSERT.

## Nejprve HTTP, až potom formulářová knihovna

Obyčejný HTML formulář umí odeslat hodnoty. Sám ale nezaručí, že request přišel z našeho prohlížeče nebo že cena a sklad dávají smysl. Každý může vytvořit vlastní POST request. Proto validaci vždy provádí server.

```text
GET /product/create → formulář
POST /product/create → Nette Form → validace → repository INSERT
                                                    ↓
                                        303 redirect → GET /product
```

Tento vzor se jmenuje **Post/Redirect/Get (PRG)**. Bez přesměrování by prohlížeč při obnovení stránky nabídl znovu odeslat stejný POST.

> **🧠 Nejdřív přemýšlej**
>
> Je atribut `required` v HTML důkazem, že server nikdy nedostane prázdný název? Jak lze request odeslat bez našeho formuláře?

## Nette Form je PHP objekt

**📄 Fragment:** `app/Presentation/Product/ProductPresenter.php`

```php
protected function createComponentProductForm(): Form
{
	$form = new Form;
	$form->addCheckbox('active', 'Produkt je aktivní')->setDefaultValue(true);
	$form->addText('code', 'Kód')->setRequired('Zadejte kód produktu.')->setMaxLength(40);
	$form->addText('name', 'Název')->setRequired('Zadejte název produktu.')->setMaxLength(160);
	$form->addTextArea('description', 'Popis')->setMaxLength(2000);
	$form->addInteger('stock', 'Sklad')->setRequired('Zadejte stav skladu.')
		->addRule(Form::Min, 'Sklad nesmí být záporný.', 0);
	$form->addFloat('price', 'Cena')->setRequired('Zadejte cenu.')
		->addRule(Form::Min, 'Cena nesmí být záporná.', 0);
	$form->addSubmit('save', 'Přidat produkt');
	$form->onSuccess[] = $this->productFormSucceeded(...);
	return $form;
}
```

Je to výukový fragment: ve finálním souboru se popisek tlačítka mění podle editace. `new Form` vytvoří objekt, `addText()` přidá control a metoda `setRequired()` vrací tentýž control, proto lze volání řetězit.

### Co znamená callback

```php
$form->onSuccess[] = $this->productFormSucceeded(...);
```

`onSuccess` je pole callbacků. `[]` přidá další prvek na konec pole. `$this->productFormSucceeded(...)` vytvoří callable – odkaz na metodu aktuálního presenteru. Nette ji zavolá až když zpracování formuláře uspěje.

## Druhá vrstva validace

**📄 Fragment:** `app/Model/Product/ProductInputValidator.php`

```php
if ($code === '' || !preg_match('/^[A-Z0-9][A-Z0-9-]{2,39}$/', $code)) {
	$errors[] = 'Kód musí mít 3–40 znaků a obsahovat velká písmena, číslice nebo pomlčky.';
}
if (!is_numeric($price) || (float) $price < 0) {
	$errors[] = 'Cena musí být nezáporné číslo.';
}
```

Formulář poskytuje dobré chybové zprávy a klientské pohodlí. Modelový validátor chrání stejné pravidlo i při CSV importu – jeden zdroj pravdy pro data z různých vstupů.

> **⚠️ Pozor**
>
> `addFloat()` převede vstup do tvaru, který očekává aplikace, ale pro peněžní hodnotu v databázi stále používáme `DECIMAL`. Nevynechávejte databázovou ani modelovou kontrolu jen proto, že formulář vypadá správně.

## Zpracování úspěšného formuláře

**📄 Fragment:** `ProductPresenter::productFormSucceeded()`

```php
$errors = $this->validator->validate($values);
if ($errors !== []) {
	foreach ($errors as $error) {
		$form->addError($error);
	}
	return;
}

$this->products->create($values);
$this->flashMessage('Produkt byl přidán.', 'success');
$this->redirect('default');
```

`!== []` znamená „pole chyb není prázdné“. `foreach` přidá každou chybu formuláři a `return` zastaví metodu dřív, než by se volal INSERT. `flashMessage` přežije jedno přesměrování; `redirect('default')` vytváří GET na seznam.

## Postup krok za krokem

1. Přihlaste se a otevřete `/product/create`. Podívejte se do zdroje HTML: Nette vytvořilo formulář a atributy validace, ale to není jediná ochrana.
2. Otevřete presenter a najděte každý control. Ke každému napište, zda vrací text, číslo, boolean nebo soubor.
3. Odešlete prázdný formulář. Poznamenejte si chybové zprávy a ověřte, že se nevytvořil nový řádek v MySQL.
4. Odešlete sklad `-1` a cenu `-10`. Určete, které pravidlo chybu hlásí.
5. Odešlete platná data s jedinečným kódem, například `STUDENT-01`. Po přesměrování produkt vyhledejte a ověřte, že existuje jednou.
6. Obnovte výsledný seznam. Prohlížeč nesmí nabízet opakované odeslání formuláře, protože aktuální request je GET.
7. Zkuste vytvořit druhý produkt se stejným kódem. Přečtěte chybovou větev pro `UniqueConstraintViolationException`; unikátní pravidlo platí i mimo formulář.

## Experiment: klientská a serverová ochrana

V DevTools upravte nebo odstraňte HTML atribut `min` / `required` a odešlete záporný sklad. Neberte tento postup jako útok na cizí web – pracujete výhradně na své lokální aplikaci. Zapište, že klientská kontrola se dá obejít, ale server a databáze hodnotu nepřijmou.

## Samostatný úkol

Přidejte srozumitelné pravidlo maximální délky popisu a vyzkoušejte je textem o jeden znak delším. Ověřte, že po chybě zůstanou už vyplněné hodnoty formuláře dostupné k opravě.

## Minikvíz

1. Kdy se spustí `onSuccess`? **Po úspěšném zpracování a validaci Nette Form.**
2. Nahrazuje HTML `required` serverovou kontrolu? **Ne.**
3. Proč po INSERTu přesměrováváme? **PRG brání opakovanému POST při refreshi.**
4. Kam patří `INSERT`? **Do repository, ne do Latte.**

## Kontrolní body a zdroje

- [ ] `/product/create` ukáže formulář.
- [ ] Neplatná data nevytvoří řádek.
- [ ] Platný jedinečný kód vznikne jednou a objeví se po redirectu.
- [ ] Pravidla jsou v Nette Form i ve sdíleném validátoru.

Čtěte [Nette Forms](https://doc.nette.org/en/forms), [formuláře v presenterech](https://doc.nette.org/en/forms/in-presenter), [validaci formulářů](https://doc.nette.org/en/forms/validation) a [Nette best practice: POST odkazy](https://doc.nette.org/en/best-practices/post-links).

## Stav projektu po lekci

Aplikace bezpečně vytváří produkt přes POST, validuje jej na serveru a po úspěchu používá PRG. Editaci stejným formulářem a mazání zatím doplníme.

**Příště:** načteme existující hodnoty do formuláře, vrátíme 404 pro chybné ID a mazání omezíme na POST.
