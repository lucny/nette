<?php declare(strict_types=1);

namespace App\Model\Product;


/** Jedno místo pro pravidla společná formuláři i CSV importu. */
final class ProductInputValidator
{
	/** @param array<string, mixed> $data @return list<string> */
	public function validate(array $data): array
	{
		$errors = [];
		$code = trim((string) ($data['code'] ?? ''));
		$name = trim((string) ($data['name'] ?? ''));
		$description = trim((string) ($data['description'] ?? ''));
		$stock = $data['stock'] ?? null;
		$price = $data['price'] ?? null;

		if ($code === '' || !preg_match('/^[A-Z0-9][A-Z0-9-]{2,39}$/', $code)) {
			$errors[] = 'Kód musí mít 3–40 znaků a obsahovat velká písmena, číslice nebo pomlčky.';
		}
		if ($name === '' || mb_strlen($name) > 160) {
			$errors[] = 'Název je povinný a může mít nejvýše 160 znaků.';
		}
		if (mb_strlen($description) > 2000) {
			$errors[] = 'Popis může mít nejvýše 2000 znaků.';
		}
		if (!is_int($stock) && !(is_string($stock) && filter_var($stock, FILTER_VALIDATE_INT) !== false)) {
			$errors[] = 'Sklad musí být celé číslo.';
		} elseif ((int) $stock < 0) {
			$errors[] = 'Sklad nesmí být záporný.';
		}
		if (!is_numeric($price) || (float) $price < 0) {
			$errors[] = 'Cena musí být nezáporné číslo.';
		}

		return $errors;
	}
}
