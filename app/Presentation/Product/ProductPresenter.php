<?php declare(strict_types=1);

namespace App\Presentation\Product;

use App\Model\Product\ProductInputValidator;
use App\Model\Product\ProductRepository;
use Nette;
use Nette\Application\UI\Form;


final class ProductPresenter extends Nette\Application\UI\Presenter
{
	private ?int $editingId = null;

	public function __construct(
		private ProductRepository $products,
		private ProductInputValidator $validator,
	) {
		parent::__construct();
	}

	protected function startup(): void
	{
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}
	}

	public function renderDefault(string $q = '', string $status = 'all', int $page = 1): void
	{
		$result = $this->products->search($q, $status, $page);
		$this->template->products = $result['items'];
		$this->template->paginator = $result['paginator'];
		$this->template->query = $q;
		$this->template->status = $status;
	}

	public function actionCreate(): void
	{
		$this->editingId = null;
	}

	public function actionEdit(int $id): void
	{
		$product = $this->products->find($id);
		if ($product === null) {
			$this->error('Produkt neexistuje.');
		}
		$this->editingId = $id;
		$this['productForm']->setDefaults([
			'active' => (bool) $product['active'],
			'code' => (string) $product['code'],
			'name' => (string) $product['name'],
			'description' => (string) $product['description'],
			'stock' => (int) $product['stock'],
			'price' => (float) $product['price'],
		]);
	}

	protected function createComponentProductForm(): Form
	{
		$form = new Form;
		$form->addCheckbox('active', 'Produkt je aktivní')->setDefaultValue(true);
		$form->addText('code', 'Kód')->setRequired('Zadejte kód produktu.')->setMaxLength(40);
		$form->addText('name', 'Název')->setRequired('Zadejte název produktu.')->setMaxLength(160);
		$form->addTextArea('description', 'Popis')->setMaxLength(2000);
		$form->addInteger('stock', 'Sklad')->setRequired('Zadejte stav skladu.')->addRule(Form::Min, 'Sklad nesmí být záporný.', 0);
		$form->addFloat('price', 'Cena')->setRequired('Zadejte cenu.')->addRule(Form::Min, 'Cena nesmí být záporná.', 0);
		$form->addSubmit('save', $this->editingId === null ? 'Přidat produkt' : 'Uložit změny');
		$form->onSuccess[] = $this->productFormSucceeded(...);
		return $form;
	}

	private function productFormSucceeded(Form $form, \stdClass $data): void
	{
		$values = [
			'active' => (bool) $data->active,
			'code' => strtoupper(trim((string) $data->code)),
			'name' => trim((string) $data->name),
			'description' => trim((string) $data->description),
			'stock' => (int) $data->stock,
			'price' => (float) $data->price,
		];
		$errors = $this->validator->validate($values);
		if ($errors !== []) {
			foreach ($errors as $error) {
				$form->addError($error);
			}
			return;
		}

		try {
			if ($this->editingId === null) {
				$this->products->create($values);
				$this->flashMessage('Produkt byl přidán.', 'success');
			} else {
				$this->products->update($this->editingId, $values);
				$this->flashMessage('Produkt byl upraven.', 'success');
			}
		} catch (Nette\Database\UniqueConstraintViolationException) {
			$form->addError('Kód produktu už používá jiný produkt.');
			return;
		}
		$this->redirect('default');
	}

	protected function createComponentPostForm(): Form
	{
		$form = new Form;
		$form->setMethod('post');
		return $form;
	}

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
}
