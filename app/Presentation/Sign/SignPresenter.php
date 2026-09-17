<?php declare(strict_types=1);

namespace App\Presentation\Sign;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;


final class SignPresenter extends Nette\Application\UI\Presenter
{
	public ?string $backlink = null;

	public function actionOut(): void
	{
		$this->getUser()->logout(true);
		$this->flashMessage('Byli jste odhlášeni.', 'success');
		$this->redirect('Home:default');
	}

	protected function createComponentSignInForm(): Form
	{
		$form = new Form;
		$form->addEmail('email', 'E-mail')->setRequired('Zadejte e-mail.');
		$form->addPassword('password', 'Heslo')->setRequired('Zadejte heslo.');
		$form->addSubmit('send', 'Přihlásit');
		$form->onSuccess[] = $this->signInFormSucceeded(...);
		return $form;
	}

	private function signInFormSucceeded(Form $form, \stdClass $data): void
	{
		try {
			$this->getUser()->login($data->email, $data->password);
		} catch (AuthenticationException) {
			$form->addError('Neplatný e-mail nebo heslo.');
			return;
		}

		if ($this->backlink !== null) {
			$this->restoreRequest($this->backlink);
		}
		$this->redirect('Home:default');
	}
}
