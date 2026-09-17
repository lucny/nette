<?php declare(strict_types=1);

// Tato ukázka je čisté PHP: žádný Nette ani databáze.
$code = 'NB-001';
$name = 'Notebook 15';
$price = 18990.0;
$stock = 12;
$active = true;

// Vstup zobrazený v HTML vždy escapujeme. Hodnota z formuláře by nebyla bezpečná jen proto,
// že ji poslal náš vlastní prohlížeč.
function e(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="cs">
<head><meta charset="utf-8"><title>Produkt</title></head>
<body>
	<article>
		<h1><?= e($name) ?></h1>
		<p>Kód: <?= e($code) ?></p>
		<p>Cena: <?= number_format($price, 2, ',', ' ') ?> Kč</p>
		<p><?= $active ? 'Aktivní' : 'Neaktivní' ?> · sklad: <?= $stock ?></p>
	</article>
</body>
</html>
