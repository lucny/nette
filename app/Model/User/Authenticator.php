<?php declare(strict_types=1);

namespace App\Model\User;

use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator as AuthenticatorInterface;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;


final class Authenticator implements AuthenticatorInterface
{
	public function __construct(
		private UserRepository $users,
		private Passwords $passwords,
	) {
	}

	public function authenticate(string $user, string $password): SimpleIdentity
	{
		$row = $this->users->findByEmail($user);
		if ($row === null || !$this->passwords->verify($password, (string) $row['password_hash'])) {
			throw new AuthenticationException('Neplatný e-mail nebo heslo.');
		}

		return new SimpleIdentity((int) $row['id'], ['admin'], ['email' => (string) $row['email']]);
	}
}
