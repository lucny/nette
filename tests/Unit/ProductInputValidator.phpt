<?php

require __DIR__ . '/../bootstrap.php';

use App\Model\Product\ProductInputValidator;
use Tester\Assert;

$validator = new ProductInputValidator;

Assert::same([], $validator->validate([
	'code' => 'NB-001',
	'name' => 'Notebook',
	'description' => 'Ukázka',
	'stock' => 3,
	'price' => 18990.0,
]));

$errors = $validator->validate([
	'code' => 'špatně',
	'name' => '',
	'description' => '',
	'stock' => -1,
	'price' => -5,
]);

Assert::count(4, $errors);
