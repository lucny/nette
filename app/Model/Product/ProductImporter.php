<?php declare(strict_types=1);

namespace App\Model\Product;

use Nette\Database\Explorer;


final class ProductImporter
{
	private const HEADER = ['active', 'code', 'name', 'description', 'stock', 'price'];

	public function __construct(
		private ProductRepository $products,
		private ProductInputValidator $validator,
		private Explorer $database,
	) {
	}

	/** @return array{processed: int, inserted: int, updated: int, skipped: int, errors: list<string>} */
	public function importCsv(string $contents): array
	{
		$stream = fopen('php://temp', 'r+');
		if ($stream === false) {
			throw new \RuntimeException('Nepodařilo se otevřít dočasný proud.');
		}
		fwrite($stream, $contents);
		rewind($stream);

		$header = fgetcsv($stream, separator: ';');
		if ($header === false) {
			fclose($stream);
			return ['processed' => 0, 'inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => ['Soubor je prázdný.']];
		}
		$header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
		if ($header !== self::HEADER) {
			fclose($stream);
			return ['processed' => 0, 'inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => ['Hlavička musí být: ' . implode(';', self::HEADER)]];
		}

		$result = ['processed' => 0, 'inserted' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
		$this->database->transaction(function () use ($stream, &$result): void {
			$line = 1;
			while (($row = fgetcsv($stream, separator: ';')) !== false) {
				$line++;
				if ($row === [null] || (count($row) === 1 && trim((string) $row[0]) === '')) {
					continue;
				}
				$result['processed']++;
				if (count($row) !== count(self::HEADER)) {
					$result['skipped']++;
					$result['errors'][] = "Řádek {$line}: očekáváno " . count(self::HEADER) . ' sloupců.';
					continue;
				}

				$data = array_combine(self::HEADER, array_map('trim', $row));
				$data['active'] = filter_var($data['active'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
				$data['stock'] = filter_var($data['stock'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
				$data['price'] = str_replace(',', '.', $data['price']);
				$errors = $this->validator->validate($data);
				if ($data['active'] === null) {
					$errors[] = 'Aktivita musí být true nebo false.';
				}
				if ($errors !== []) {
					$result['skipped']++;
					$result['errors'][] = "Řádek {$line}: " . implode(' ', $errors);
					continue;
				}

				if ($this->products->upsert($data)) {
					$result['updated']++;
				} else {
					$result['inserted']++;
				}
			}
		});

		fclose($stream);
		return $result;
	}
}
