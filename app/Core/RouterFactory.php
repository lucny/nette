<?php declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('api/products/<code>', 'Api:Products:default');
		$router->addRoute('product[/<action>][/<id>]', 'Product:default');
		$router->addRoute('sign[/<action>]', 'Sign:in');
		$router->addRoute('import[/<action>]', 'Import:default');
		$router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
		return $router;
	}
}
