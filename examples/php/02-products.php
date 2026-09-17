<?php declare(strict_types=1);

$products = [
	['code' => 'NB-001', 'name' => 'Notebook 15', 'active' => true, 'stock' => 12],
	['code' => 'MO-004', 'name' => 'Monitor 24', 'active' => true, 'stock' => 4],
	['code' => 'MS-003', 'name' => 'Myš bez skladu', 'active' => false, 'stock' => 0],
];
?>
<table>
	<thead><tr><th>Kód</th><th>Název</th><th>Stav</th><th>Sklad</th></tr></thead>
	<tbody>
	<?php foreach ($products as $product): ?>
		<?php if (!$product['active']) { continue; } // Podmínka vynechá neaktivní zboží. ?>
		<tr>
			<td><?= htmlspecialchars($product['code'], ENT_QUOTES, 'UTF-8') ?></td>
			<td><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
			<td><?= $product['stock'] < 5 ? 'objednat' : 'v pořádku' ?></td>
			<td><?= $product['stock'] ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
