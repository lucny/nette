<?php declare(strict_types=1);

namespace App\Model\Product;

use DateTimeImmutable;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\Paginator;


final class ProductRepository
{
	public function __construct(private Explorer $database)
	{
	}

	/** @return array{items: Selection<ActiveRow>, paginator: Paginator} */
	public function search(string $query, string $status, int $page, int $itemsPerPage = 10): array
	{
		$selection = $this->database->table('product')->order('created_at DESC');
		$query = trim($query);
		if ($query !== '') {
			$like = '%' . $query . '%';
			$selection->where('code LIKE ? OR name LIKE ?', $like, $like);
		}
		if ($status === 'active') {
			$selection->where('active', true);
		} elseif ($status === 'inactive') {
			$selection->where('active', false);
		}

		$paginator = new Paginator;
		$paginator->setItemCount($selection->count());
		$paginator->setItemsPerPage($itemsPerPage);
		$paginator->setPage(max(1, $page));

		return [
			'items' => $selection->page($paginator->getPage(), $paginator->getItemsPerPage()),
			'paginator' => $paginator,
		];
	}

	public function find(int $id): ?ActiveRow
	{
		return $this->database->table('product')->get($id);
	}

	public function findByCode(string $code): ?ActiveRow
	{
		return $this->database->table('product')->where('code', $code)->fetch();
	}

	/** @param array{active: bool, code: string, name: string, description: string, stock: int, price: float|string} $data */
	public function create(array $data): ActiveRow
	{
		$now = new DateTimeImmutable;
		return $this->database->table('product')->insert([
			'active' => $data['active'],
			'code' => strtoupper(trim($data['code'])),
			'name' => trim($data['name']),
			'description' => trim($data['description']),
			'stock' => $data['stock'],
			'price' => $data['price'],
			'created_at' => $now,
			'updated_at' => $now,
		]);
	}

	/** @param array{active: bool, code: string, name: string, description: string, stock: int, price: float|string} $data */
	public function update(int $id, array $data): void
	{
		$this->database->table('product')->where('id', $id)->update([
			'active' => $data['active'],
			'code' => strtoupper(trim($data['code'])),
			'name' => trim($data['name']),
			'description' => trim($data['description']),
			'stock' => $data['stock'],
			'price' => $data['price'],
			'updated_at' => new DateTimeImmutable,
		]);
	}

	public function delete(int $id): void
	{
		$this->database->table('product')->where('id', $id)->delete();
	}

	/** @param array{active: bool, code: string, name: string, description: string, stock: int, price: float|string} $data */
	public function upsert(array $data): bool
	{
		$existing = $this->findByCode(strtoupper(trim($data['code'])));
		if ($existing === null) {
			$this->create($data);
			return false;
		}

		$this->update($existing->id, $data);
		return true;
	}
}
