<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class SyncProductQuantities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:sync-quantities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize all product quantities from their validated sourcings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting product quantity synchronization...');
        
        $products = Product::with('sourcings')->get();
        $updatedCount = 0;
        
        foreach ($products as $product) {
            $oldQuantity = $product->quantity;
            $product->syncQuantityFromSourcings();
            $newQuantity = $product->quantity;
            
            if ($oldQuantity != $newQuantity) {
                $this->line("Product '{$product->name}': {$oldQuantity} → {$newQuantity}");
                $updatedCount++;
            }
        }
        
        $this->info("Synchronization complete! Updated {$updatedCount} product(s).");
        
        return 0;
    }
}
