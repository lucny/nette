<?php declare(strict_types=1);

namespace App\Presentation\Api;

use App\Model\Product\ProductRepository;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;


final class ProductsPresenter extends Presenter
{
	public function __construct(private ProductRepository $products)
	{
		parent::__construct();
	}

	public function renderDefault(string $code): void
	{
		$product = $this->products->findByCode(strtoupper($code));
		if ($product === null) {
			$this->getHttpResponse()->setCode(404);
			$this->sendResponse(new JsonResponse(['error' => 'Produkt nebyl nalezen.', 'code' => $code]));
		}

		$createdAt = $product['created_at'];
		$updatedAt = $product['updated_at'];
		$this->sendResponse(new JsonResponse([
			'code' => (string) $product['code'],
			'name' => (string) $product['name'],
			'description' => (string) $product['description'],
			'stock' => (int) $product['stock'],
			'price' => (float) $product['price'],
			'active' => (bool) $product['active'],
			'createdAt' => $createdAt instanceof \DateTimeInterface ? $createdAt->format(DATE_ATOM) : (string) $createdAt,
			'updatedAt' => $updatedAt instanceof \DateTimeInterface ? $updatedAt->format(DATE_ATOM) : (string) $updatedAt,
		]));
	}
}
