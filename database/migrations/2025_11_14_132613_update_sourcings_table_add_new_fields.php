<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add temporary column to store country_id from products
        Schema::table('sourcings', function (Blueprint $table) {
            $table->unsignedBigInteger('temp_country_id')->nullable()->after('product_id');
        });

        // Step 2: Copy country_id from products to temp column
        DB::statement('UPDATE sourcings s 
                       INNER JOIN products p ON s.product_id = p.id 
                       SET s.temp_country_id = p.country_id 
                       WHERE s.product_id IS NOT NULL');

        // Step 3: Get first country as default for sourcings without valid country
        $defaultCountryId = DB::table('countries')->value('id');
        if ($defaultCountryId) {
            DB::table('sourcings')
                ->whereNull('temp_country_id')
                ->update(['temp_country_id' => $defaultCountryId]);
        }

        // Step 4: Add temporary columns to store product data
        Schema::table('sourcings', function (Blueprint $table) {
            $table->string('temp_product_name')->nullable()->after('product_id');
            $table->string('temp_product_image')->nullable()->after('temp_product_name');
            $table->unsignedBigInteger('temp_category_id')->nullable()->after('temp_product_image');
        });

        // Step 5: Copy product data from products table
        DB::statement('UPDATE sourcings s 
                       INNER JOIN products p ON s.product_id = p.id 
                       SET s.temp_product_name = p.name,
                           s.temp_product_image = p.image,
                           s.temp_category_id = p.category_id
                       WHERE s.product_id IS NOT NULL');

        // Step 6: Drop foreign key and product_id, add new fields
        Schema::table('sourcings', function (Blueprint $table) {
            // Drop foreign key constraint and product_id column
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');

            // Add new fields (without foreign keys first, country_id nullable temporarily)
            $table->string('product_name')->after('id');
            $table->string('product_image')->nullable()->after('product_name');
            $table->unsignedBigInteger('category_id')->nullable()->after('product_image');
            $table->unsignedInteger('quantity')->default(0)->after('category_id');
            $table->unsignedBigInteger('country_id')->nullable()->after('quantity');
            $table->decimal('price', 12, 2)->default(0)->after('country_id');
            $table->decimal('cost', 12, 2)->default(0)->after('price');
            $table->enum('shipping_type', ['in_transit', 'arrived'])->nullable()->after('cost');
            $table->decimal('additional_fees', 12, 2)->default(0)->after('shipping_type');
            $table->decimal('testing_fees', 12, 2)->default(0)->after('additional_fees');
            $table->unsignedBigInteger('supplier_id')->nullable()->after('testing_fees');
        });

        // Step 7: Copy data from temp columns to new columns
        if ($defaultCountryId) {
            DB::statement("UPDATE sourcings SET 
                           product_name = COALESCE(temp_product_name, 'Unknown Product'),
                           product_image = temp_product_image,
                           category_id = temp_category_id,
                           country_id = COALESCE(temp_country_id, {$defaultCountryId})");
        } else {
            // Only copy records with valid country_id
            DB::statement("UPDATE sourcings SET 
                           product_name = COALESCE(temp_product_name, 'Unknown Product'),
                           product_image = temp_product_image,
                           category_id = temp_category_id,
                           country_id = temp_country_id
                           WHERE temp_country_id IS NOT NULL");
        }
        
        // Step 8: Set default for any remaining nulls (safety check)
        if ($defaultCountryId) {
            DB::table('sourcings')
                ->whereNull('country_id')
                ->update(['country_id' => $defaultCountryId]);
            
            DB::table('sourcings')
                ->whereNull('product_name')
                ->orWhere('product_name', '')
                ->update(['product_name' => 'Unknown Product']);
        } else {
            // If no default country and we have null country_id, throw error
            $nullCount = DB::table('sourcings')->whereNull('country_id')->count();
            if ($nullCount > 0) {
                throw new \Exception('No countries found in database and some sourcings have no valid country_id. Please create at least one country before running this migration.');
            }
        }

        // Step 9: Make country_id not nullable and drop temp columns
        Schema::table('sourcings', function (Blueprint $table) {
            // Make country_id not nullable
            $table->unsignedBigInteger('country_id')->nullable(false)->change();
            
            // Drop temp columns
            $table->dropColumn(['temp_country_id', 'temp_product_name', 'temp_product_image', 'temp_category_id']);
        });

        // Step 10: Add foreign key constraints (after ensuring all data is valid)
        Schema::table('sourcings', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sourcings', function (Blueprint $table) {
            // Drop new foreign keys and columns
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['country_id']);
            $table->dropForeign(['category_id']);
            
            $table->dropColumn([
                'product_name',
                'product_image',
                'category_id',
                'quantity',
                'country_id',
                'price',
                'cost',
                'shipping_type',
                'additional_fees',
                'testing_fees',
                'supplier_id',
            ]);

            // Restore product_id
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        });
    }
};
