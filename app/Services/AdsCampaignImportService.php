<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdsCampaignImportService
{
	/**
	 * Extract the product import ID from a campaign name (e.g. "ug01- Antifuite" → "ug01").
	 */
	public static function extractImportId(string $campaignName): ?string
	{
		$parts = explode('-', $campaignName, 2);
		$code = trim($parts[0]);

		return $code !== '' ? $code : null;
	}

	/**
	 * @return array<int, array{name: string, amount_spent: float, leads: int}>
	 */
	public function parse(UploadedFile $file): array
	{
		$extension = strtolower($file->getClientOriginalExtension());

		if ($extension === 'csv') {
			return $this->parseCsv($file->getRealPath());
		}

		$spreadsheet = IOFactory::load($file->getRealPath());
		$rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

		return $this->parseRows($rows);
	}

	/**
	 * @return array<int, array{name: string, amount_spent: float, leads: int}>
	 */
	private function parseCsv(string $path): array
	{
		$rows = [];
		$handle = fopen($path, 'r');

		if ($handle === false) {
			throw new \RuntimeException('Unable to read the uploaded file.');
		}

		while (($row = fgetcsv($handle)) !== false) {
			$rows[] = $row;
		}

		fclose($handle);

		return $this->parseRows($rows);
	}

	/**
	 * @param  array<int, array<int, mixed>>  $rows
	 * @return array<int, array{name: string, amount_spent: float, leads: int}>
	 */
	private function parseRows(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$headerRow = array_shift($rows);
		$columnMap = $this->mapColumns($headerRow);

		if ($columnMap['name'] === null || $columnMap['amount_spent'] === null || $columnMap['leads'] === null) {
			throw new \InvalidArgumentException(
				'The file must contain columns for Campaign name, Amount spent, and Results.'
			);
		}

		$campaigns = [];

		foreach ($rows as $row) {
			$name = trim((string) ($row[$columnMap['name']] ?? ''));
			$amountSpent = $this->parseNumber($row[$columnMap['amount_spent']] ?? null);
			$leads = $this->parseInteger($row[$columnMap['leads']] ?? null);

			if ($name === '' && $amountSpent === null && $leads === null) {
				continue;
			}

			if ($name === '') {
				continue;
			}

			$campaigns[] = [
				'name' => $name,
				'amount_spent' => $amountSpent ?? 0,
				'leads' => $leads ?? 0,
			];
		}

		return $campaigns;
	}

	/**
	 * @param  array<int, mixed>  $headerRow
	 * @return array{name: ?int, amount_spent: ?int, leads: ?int}
	 */
	private function mapColumns(array $headerRow): array
	{
		$map = [
			'name' => null,
			'amount_spent' => null,
			'leads' => null,
		];

		foreach ($headerRow as $index => $header) {
			$normalized = strtolower(trim((string) $header));
			$normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

			if ($this->matchesAny($normalized, ['campaign name', 'campaign', 'name'])) {
				$map['name'] = $index;
			} elseif ($this->matchesAny($normalized, ['amount spent (usd)', 'amount spent', 'spent', 'amount'])) {
				$map['amount_spent'] = $index;
			} elseif ($this->matchesAny($normalized, ['results', 'leads', 'result'])) {
				$map['leads'] = $index;
			}
		}

		return $map;
	}

	private function matchesAny(string $value, array $needles): bool
	{
		foreach ($needles as $needle) {
			if ($value === $needle || str_contains($value, $needle)) {
				return true;
			}
		}

		return false;
	}

	private function parseNumber(mixed $value): ?float
	{
		if ($value === null || $value === '') {
			return null;
		}

		$cleaned = str_replace([',', ' '], '', (string) $value);
		$cleaned = preg_replace('/[^0-9.\-]/', '', $cleaned) ?? '';

		if ($cleaned === '' || ! is_numeric($cleaned)) {
			return null;
		}

		return (float) $cleaned;
	}

	private function parseInteger(mixed $value): ?int
	{
		$number = $this->parseNumber($value);

		return $number === null ? null : (int) round($number);
	}
}
