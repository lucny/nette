<?php declare(strict_types=1);

namespace App\Presentation\Import;

use App\Model\Product\ProductImporter;
use Nette;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;


final class ImportPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(private ProductImporter $importer)
	{
		parent::__construct();
	}

	protected function startup(): void
	{
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in', ['backlink' => $this->storeRequest()]);
		}
	}

	protected function createComponentImportForm(): Form
	{
		$form = new Form;
		$form->addUpload('csv', 'CSV soubor')
			->setRequired('Vyberte CSV soubor.')
			->addRule(Form::MaxFileSize, 'Soubor může mít nejvýše 2 MB.', 2 * 1024 * 1024)
			->addRule(Form::MimeType, 'Povoleno je pouze CSV nebo textový soubor.', ['text/csv', 'text/plain', 'application/vnd.ms-excel']);
		$form->addSubmit('send', 'Spustit import');
		$form->onSuccess[] = $this->importFormSucceeded(...);
		return $form;
	}

	private function importFormSucceeded(Form $form, \stdClass $data): void
	{
		/** @var FileUpload $upload */
		$upload = $data->csv;
		if (!$upload->isOk()) {
			$form->addError('Nahrání souboru selhalo.');
			return;
		}
		$this->template->result = $this->importer->importCsv($upload->getContents() ?? '');
	}
}
