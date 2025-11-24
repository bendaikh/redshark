<?php

namespace App\Console\Commands;

use App\Models\Sourcing;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LinkSourcingsToProducts extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sourcings:link-products {--dry-run : Run without making changes}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Automatically link sourcings with NULL product_id to existing products';

	/**
	 * Find matching product for a sourcing
	 */
	private function findMatchingProduct(Sourcing $sourcing): ?Product
	{
		// Clean the sourcing product name (remove country codes like CIV, RDC, etc.)
		$cleanName = $this->cleanProductName($sourcing->product_name);
		
		// Strategy 1: Exact match with cleaned name
		$product = Product::where(DB::raw('LOWER(name)'), '=', strtolower($cleanName))
			->when($sourcing->category_id, fn($q) => $q->where('category_id', $sourcing->category_id))
			->when($sourcing->country_id, fn($q) => $q->where('country_id', $sourcing->country_id))
			->first();
		
		if ($product) return $product;
		
		// Strategy 2: Exact match with cleaned name, ignore country
		$product = Product::where(DB::raw('LOWER(name)'), '=', strtolower($cleanName))
			->when($sourcing->category_id, fn($q) => $q->where('category_id', $sourcing->category_id))
			->first();
		
		if ($product) return $product;
		
		// Strategy 3: Exact match with original name
		$product = Product::where(DB::raw('LOWER(name)'), '=', strtolower($sourcing->product_name))
			->when($sourcing->category_id, fn($q) => $q->where('category_id', $sourcing->category_id))
			->first();
		
		if ($product) return $product;
		
		// Strategy 4: Partial match - product name contains sourcing name (cleaned)
		$product = Product::where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($cleanName) . '%')
			->when($sourcing->category_id, fn($q) => $q->where('category_id', $sourcing->category_id))
			->first();
		
		if ($product) return $product;
		
		// Strategy 5: Partial match - sourcing name contains product name
		$allProducts = Product::all();
		foreach ($allProducts as $p) {
			if (stripos($cleanName, $p->name) !== false) {
				// If category matches too, it's a good match
				if ($sourcing->category_id && $p->category_id == $sourcing->category_id) {
					return $p;
				}
			}
		}
		
		// Strategy 6: Just name similarity
		foreach ($allProducts as $p) {
			if (stripos($cleanName, $p->name) !== false || stripos($p->name, $cleanName) !== false) {
				return $p;
			}
		}
		
		return null;
	}
	
	/**
	 * Clean product name by removing common country codes and suffixes
	 */
	private function cleanProductName(string $name): string
	{
		// Remove common country codes (CIV, RDC, etc.) from the end
		$name = preg_replace('/\s+(CIV|RDC|CI|CD|BF|SN|TG|BJ|NE|ML|MR)\s*$/i', '', $name);
		
		// Trim whitespace
		return trim($name);
	}

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$dryRun = $this->option('dry-run');
		
		if ($dryRun) {
			$this->info('Running in DRY-RUN mode. No changes will be made.');
		}

		// Get all sourcings without product_id
		$sourcingsWithoutProduct = Sourcing::whereNull('product_id')->get();

		if ($sourcingsWithoutProduct->isEmpty()) {
			$this->info('✓ No sourcings found with NULL product_id. Everything is already linked!');
			return 0;
		}

		$this->info("Found {$sourcingsWithoutProduct->count()} sourcings without product_id.");
		$this->newLine();

		$matched = 0;
		$notMatched = 0;
		$matchDetails = [];

		foreach ($sourcingsWithoutProduct as $sourcing) {
			$product = $this->findMatchingProduct($sourcing);

			
			if ($product) {
				$matchDetails[] = [
					'sourcing_id' => $sourcing->id,
					'sourcing_name' => $sourcing->product_name,
					'product_id' => $product->id,
					'product_name' => $product->name,
					'sourcing_date' => $sourcing->sourcing_date?->format('Y-m-d') ?? 'N/A',
				];
				
				if (!$dryRun) {
					$sourcing->update(['product_id' => $product->id]);
				}
				
				$matched++;
			} else {
				$this->warn("⚠ No match found for sourcing #{$sourcing->id}: {$sourcing->product_name}");
				$notMatched++;
			}
		}

		// Display results
		$this->newLine();
		
		if ($matched > 0) {
			$this->info("✓ Successfully matched {$matched} sourcing(s) to products:");
			$this->newLine();
			
			$this->table(
				['Sourcing ID', 'Sourcing Name', 'Product ID', 'Product Name', 'Sourcing Date'],
				array_map(function($detail) {
					return [
						$detail['sourcing_id'],
						$detail['sourcing_name'],
						$detail['product_id'],
						$detail['product_name'],
						$detail['sourcing_date'],
					];
				}, $matchDetails)
			);
		}
		
		if ($notMatched > 0) {
			$this->newLine();
			$this->warn("⚠ {$notMatched} sourcing(s) could not be matched to any product.");
			$this->info("These sourcings may need to be validated to create new products or matched manually.");
		}
		
		$this->newLine();
		
		if ($dryRun) {
			$this->info('DRY-RUN complete. Run without --dry-run to apply changes.');
		} else {
			$this->info('✓ Linking process complete!');
		}

		return 0;
	}
}

