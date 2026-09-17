<?php declare(strict_types=1);

namespace App\Model\User;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;


final class UserRepository
{
	public function __construct(private Explorer $database)
	{
	}

	public function findByEmail(string $email): ?ActiveRow
	{
		return $this->database->table('user')->where('email', strtolower(trim($email)))->fetch();
	}
}
