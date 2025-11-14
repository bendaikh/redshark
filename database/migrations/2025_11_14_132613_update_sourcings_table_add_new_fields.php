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
        // Check if product_id column exists
        $hasProductId = Schema::hasColumn('sourcings', 'product_id');
        $defaultCountryId = DB::table('countries')->value('id');

        if (!$defaultCountryId) {
            throw new \Exception('No countries found in database. Please create at least one country before running this migration.');
        }

        if ($hasProductId) {
            // Step 1: Add temporary column to store country_id from products
            Schema::table('sourcings', function (Blueprint $table) {
                $table->unsignedBigInteger('temp_country_id')->nullable();
            });

            // Step 2: Copy country_id from products to temp column
            DB::statement('UPDATE sourcings s 
                           INNER JOIN products p ON s.product_id = p.id 
                           SET s.temp_country_id = p.country_id 
                           WHERE s.product_id IS NOT NULL');

            // Step 3: Set default country for sourcings without valid country
            DB::table('sourcings')
                ->whereNull('temp_country_id')
                ->update(['temp_country_id' => $defaultCountryId]);

            // Step 4: Add temporary columns to store product data
            Schema::table('sourcings', function (Blueprint $table) {
                $table->string('temp_product_name')->nullable();
                $table->string('temp_product_image')->nullable();
                $table->unsignedBigInteger('temp_category_id')->nullable();
            });

            // Step 5: Copy product data from products table
            DB::statement('UPDATE sourcings s 
                           INNER JOIN products p ON s.product_id = p.id 
                           SET s.temp_product_name = p.name,
                               s.temp_product_image = p.image,
                               s.temp_category_id = p.category_id
                           WHERE s.product_id IS NOT NULL');

            // Set defaults for sourcings without products
            DB::table('sourcings')
                ->whereNull('temp_product_name')
                ->update([
                    'temp_product_name' => 'Unknown Product',
                    'temp_country_id' => $defaultCountryId,
                ]);

            // Step 6: Drop foreign key and product_id
            Schema::table('sourcings', function (Blueprint $table) {
                // Check if foreign key exists before dropping
                $connection = Schema::getConnection();
                $db = $connection->getDatabaseName();
                $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'sourcings' AND COLUMN_NAME = 'product_id' AND REFERENCED_TABLE_NAME IS NOT NULL", [$db]);
                
                if (!empty($foreignKeys)) {
                    $table->dropForeign([$foreignKeys[0]->CONSTRAINT_NAME]);
                }
                $table->dropColumn('product_id');
            });
        } else {
            // product_id doesn't exist, just add temp columns without copying from products
            Schema::table('sourcings', function (Blueprint $table) {
                $table->unsignedBigInteger('temp_country_id')->nullable();
                $table->string('temp_product_name')->nullable();
                $table->string('temp_product_image')->nullable();
                $table->unsignedBigInteger('temp_category_id')->nullable();
            });

            // Set defaults for all sourcings
            DB::table('sourcings')
                ->whereNull('temp_country_id')
                ->update([
                    'temp_country_id' => $defaultCountryId,
                    'temp_product_name' => 'Unknown Product',
                ]);
        }

        // Step 7: Check if new columns already exist to avoid duplicates
        $hasNewColumns = Schema::hasColumn('sourcings', 'product_name');
        
        if (!$hasNewColumns) {
            // Add new fields (without foreign keys first, country_id nullable temporarily)
            Schema::table('sourcings', function (Blueprint $table) {
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
        }

        // Step 8: Copy data from temp columns to new columns (only if temp columns exist)
        $hasTempColumns = Schema::hasColumn('sourcings', 'temp_product_name');
        
        if ($hasTempColumns) {
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
        } else {
            // No temp columns, just set defaults for new columns
            DB::table('sourcings')
                ->whereNull('product_name')
                ->update([
                    'product_name' => 'Unknown Product',
                    'country_id' => $defaultCountryId,
                ]);
        }
        
        // Step 9: Set default for any remaining nulls (safety check)
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

        // Step 10: Make country_id not nullable and drop temp columns (if they exist)
        Schema::table('sourcings', function (Blueprint $table) {
            // Make country_id not nullable
            $table->unsignedBigInteger('country_id')->nullable(false)->change();
            
            // Drop temp columns if they exist
            $tempColumns = [];
            if (Schema::hasColumn('sourcings', 'temp_country_id')) {
                $tempColumns[] = 'temp_country_id';
            }
            if (Schema::hasColumn('sourcings', 'temp_product_name')) {
                $tempColumns[] = 'temp_product_name';
            }
            if (Schema::hasColumn('sourcings', 'temp_product_image')) {
                $tempColumns[] = 'temp_product_image';
            }
            if (Schema::hasColumn('sourcings', 'temp_category_id')) {
                $tempColumns[] = 'temp_category_id';
            }
            
            if (!empty($tempColumns)) {
                $table->dropColumn($tempColumns);
            }
        });

        // Step 11: Add foreign key constraints (after ensuring all data is valid) - only if they don't exist
        $connection = Schema::getConnection();
        $db = $connection->getDatabaseName();
        
        $existingForeignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'sourcings' AND REFERENCED_TABLE_NAME IS NOT NULL", [$db]);
        $existingKeys = array_column($existingForeignKeys, 'CONSTRAINT_NAME');
        
        Schema::table('sourcings', function (Blueprint $table) use ($existingKeys) {
            if (!in_array('sourcings_category_id_foreign', $existingKeys)) {
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            }
            if (!in_array('sourcings_country_id_foreign', $existingKeys)) {
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            }
            if (!in_array('sourcings_supplier_id_foreign', $existingKeys)) {
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            }
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
