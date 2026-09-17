<?php declare(strict_types=1);

use App\Bootstrap;
use Nette\Database\Explorer;
use Nette\Security\Passwords;

require __DIR__ . '/../vendor/autoload.php';

if ($argc < 3) {
	fwrite(STDERR, "Použití: php bin/create-user.php student@example.test heslo\n");
	exit(1);
}

$email = strtolower(trim($argv[1]));
$password = $argv[2];
$bootstrap = new Bootstrap;
$container = $bootstrap->bootWebApplication();
$database = $container->getByType(Explorer::class);
$passwords = $container->getByType(Passwords::class);

$database->table('user')->insert([
	'email' => $email,
	'password_hash' => $passwords->hash($password),
	'created_at' => new DateTimeImmutable,
]);

fwrite(STDOUT, "Uživatel {$email} byl vytvořen. Heslo se uložilo pouze jako hash.\n");
