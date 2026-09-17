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

		$this->sendResponse(new JsonResponse([
			'code' => $product->code,
			'name' => $product->name,
			'description' => $product->description,
			'stock' => (int) $product->stock,
			'price' => (float) $product->price,
			'active' => (bool) $product->active,
			'createdAt' => $product->created_at->format(DATE_ATOM),
			'updatedAt' => $product->updated_at->format(DATE_ATOM),
		]));
	}
}
