# Lekce 10 – Nette Forms a přidání produktu

**Čas:** 2 × 45 minut  
**Výchozí stav:** přihlášený seznam produktů s repository.

## Co dnes vytvoříme

Komponentu `createComponentProductForm()` v `ProductPresenter`. Formulář ověří aktivitu, kód, název, popis, sklad a cenu a po úspěchu INSERTne produkt.

## Co se naučíme

- porovnat ruční HTML form a Nette Form,
- vysvětlit control, hodnotu, `required`, `addRule`, callback a `onSuccess`,
- pochopit, že klientská validace nenahrazuje server,
- použít Post/Redirect/Get po uložení.

## Kde jsme skončili

Seznam čte z databáze. Nemáme bezpečný způsob, jak vložit nový řádek.

## Nové pojmy

Form, control, callback, `onSuccess`, `stdClass`, `INSERT`, PRG, serverová validace.

## PHP princip

```php
$form->onSuccess[] = $this->productFormSucceeded(...);
```

Zápis předává metodu jako callable. Nette ji zavolá po úspěšném zpracování formuláře. Callback dostane formulář a data; data nejsou důvodem k vynechání další validace v modelu.

## Nette princip

`Nette\Application\UI\Form` je komponenta presenteru. Generuje HTML, načte POST, validuje a vystaví chyby. Po uložení presenter přesměruje na seznam, takže obnovení stránky neopakuje POST.

## Jak to funguje

```text
POST form → Nette Form → validace → ProductRepository::create()
                                  ↓
                              redirect GET /product
```

## Postup krok za krokem

1. V `ProductPresenter` vytvoř controls přes `addCheckbox`, `addText`, `addTextArea`, `addInteger`, `addFloat` a `addSubmit`.
2. Povinné hodnoty označ `setRequired`. Rozsah skladu a ceny omez `addRule(Form::Min, ...)`.
3. V callbacku sestav `$values`. Komentář vysvětlí, proč se normalizuje kód a proč se cena převádí až po validaci.
4. `ProductInputValidator` zopakuje pravidla, protože POST může přijít mimo náš formulář.
5. Při úspěchu zavolej `create()`, nastav flash message a `redirect('default')`.
6. Zkus prázdný název a záporný sklad. Ověř, že server chybu odmítne i při ručním POST.

## Co se právě stalo

HTML `required` zlepšuje UX, ale útočník může poslat vlastní request. Server musí data ověřit znovu. PRG oddělí změnu dat od následného načtení seznamu.

## Experiment

V Developer Tools vypni klientskou validaci nebo odešli ruční POST s `stock=-1`. Zapiš, která vrstva chybu zastavila.

## Miniúkol

Přidej pravidlo maximální délky popisu a uživatelskou chybovou zprávu v češtině. Ověř, že se stará hodnota ve formuláři zachová.

## Minikvíz

1. Kdy se spustí `onSuccess`? **Po úspěšné validaci formuláře.**
2. Nahrazuje `required` serverovou kontrolu? **Ne.**
3. Proč redirect po INSERTu? **PRG zabrání opakování POST při reloadu.**
4. Kam patří SQL insert? **Do repository/modelu.**

## Nejčastější chyby

- `onSuccess` bez `isSuccess`/bez napojení callbacku,
- ukládání dat před validací,
- cena v Latte nebo formuláři jako nesmyslný text,
- chybějící redirect,
- kontrola pouze přes HTML atribut.

## Kontrolní body

- `/product/create` zobrazí formulář,
- chybná data se neuloží,
- správná data vytvoří řádek,
- po uložení následuje GET seznamu.

## Shrnutí

Nette Forms zjednodušují rutinu, ale princip bezpečnosti zůstává: nedůvěřovat vstupu, validovat na serveru a po změně použít redirect.

## Co bude příště

Stejný formulář znovu použijeme pro editaci a mazání ochráníme POST signálem se same-origin omezením.

## Stav projektu po lekci

- Funguje přidání produktu přes Nette Form.
- Editace a mazání ještě nejsou hotové.
- Přibyly formulářové šablony a `ProductInputValidator`.

## Poznámka pro učitele

Nechte studenty rozlišit HTML control a PHP objekt control. Při nedostatku času vynechte ruční reprodukci POSTu, ale nesmí se přeskočit serverová validace a PRG.
