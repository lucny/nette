<?php declare(strict_types=1);

/** @param array<int, array<string, mixed>> $products */
function filterProducts(array $products, ?string $query): array
{
	$query = strtolower(trim((string) $query));
	if ($query === '') {
		return $products;
	}

	return array_values(array_filter($products, static function (array $product) use ($query): bool {
		return str_contains(strtolower((string) $product['code']), $query)
			|| str_contains(strtolower((string) $product['name']), $query);
	}));
}

$products = [
	['code' => 'NB-001', 'name' => 'Notebook 15'],
	['code' => 'KB-002', 'name' => 'Klávesnice'],
];
$query = $_GET['q'] ?? null; // GET je vhodný pro filtr, protože nemění data.
$filtered = filterProducts($products, is_string($query) ? $query : null);
foreach ($filtered as $product) {
	echo htmlspecialchars($product['code'] . ' – ' . $product['name'], ENT_QUOTES, 'UTF-8') . "<br>";
}
