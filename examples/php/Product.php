<?php declare(strict_types=1);

namespace Course\Example;

final class Product
{
	public function __construct(
		private string $code,
		private string $name,
	) {
	}

	public function label(): string
	{
		return $this->code . ' – ' . $this->name;
	}
}
