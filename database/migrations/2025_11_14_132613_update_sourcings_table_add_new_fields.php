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
        
        // Check if there's any data to migrate
        $sourcingsCount = DB::table('sourcings')->count();

        // Only require countries if there's existing data to migrate
        if (!$defaultCountryId && $sourcingsCount > 0) {
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
            if ($defaultCountryId) {
                DB::table('sourcings')
                    ->whereNull('temp_country_id')
                    ->update(['temp_country_id' => $defaultCountryId]);
            }

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
            $updateData = ['temp_product_name' => 'Unknown Product'];
            if ($defaultCountryId) {
                $updateData['temp_country_id'] = $defaultCountryId;
            }
            DB::table('sourcings')
                ->whereNull('temp_product_name')
                ->update($updateData);

            // Step 6: Drop foreign key and product_id
            Schema::table('sourcings', function (Blueprint $table) {
                // Try to drop foreign key using standard Laravel naming convention
                try {
                    $table->dropForeign(['product_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist or have a different name, that's okay
                    \Log::info("Could not drop foreign key for product_id: " . $e->getMessage());
                }
                
                // Drop the column
                if (Schema::hasColumn('sourcings', 'product_id')) {
                    $table->dropColumn('product_id');
                }
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
            $updateData = ['temp_product_name' => 'Unknown Product'];
            if ($defaultCountryId) {
                $updateData['temp_country_id'] = $defaultCountryId;
            }
            DB::table('sourcings')
                ->whereNull('temp_country_id')
                ->update($updateData);
        }

        // Step 7: Add new columns individually if they don't exist
        // Define columns in order with their dependencies
        $columnsToAdd = [
            'product_name' => ['type' => 'string', 'nullable' => false, 'after' => 'id'],
            'product_image' => ['type' => 'string', 'nullable' => true, 'after' => 'product_name'],
            'category_id' => ['type' => 'unsignedBigInteger', 'nullable' => true, 'after' => 'product_image'],
            'quantity' => ['type' => 'unsignedInteger', 'nullable' => false, 'default' => 0, 'after' => 'category_id'],
            'country_id' => ['type' => 'unsignedBigInteger', 'nullable' => true, 'after' => 'quantity'],
            'price' => ['type' => 'decimal', 'nullable' => false, 'default' => 0, 'precision' => [12, 2], 'after' => 'country_id'],
            'cost' => ['type' => 'decimal', 'nullable' => false, 'default' => 0, 'precision' => [12, 2], 'after' => 'price'],
            'shipping_type' => ['type' => 'enum', 'nullable' => true, 'values' => ['in_transit', 'arrived'], 'after' => 'cost'],
            'additional_fees' => ['type' => 'decimal', 'nullable' => false, 'default' => 0, 'precision' => [12, 2], 'after' => 'shipping_type'],
            'testing_fees' => ['type' => 'decimal', 'nullable' => false, 'default' => 0, 'precision' => [12, 2], 'after' => 'additional_fees'],
            'supplier_id' => ['type' => 'unsignedBigInteger', 'nullable' => true, 'after' => 'testing_fees'],
        ];
        
        // Add columns one by one in order, checking if they exist and if the 'after' column exists
        foreach ($columnsToAdd as $columnName => $columnDef) {
            if (!Schema::hasColumn('sourcings', $columnName)) {
                try {
                    // Check if the 'after' column exists before the closure
                    $useAfter = isset($columnDef['after']) && Schema::hasColumn('sourcings', $columnDef['after']);
                    $afterColumn = $useAfter ? $columnDef['after'] : null;
                    
                    Schema::table('sourcings', function (Blueprint $table) use ($columnName, $columnDef, $afterColumn) {
                        switch ($columnDef['type']) {
                            case 'string':
                                $column = $table->string($columnName);
                                break;
                            case 'unsignedBigInteger':
                                $column = $table->unsignedBigInteger($columnName);
                                break;
                            case 'unsignedInteger':
                                $column = $table->unsignedInteger($columnName);
                                break;
                            case 'decimal':
                                $column = $table->decimal($columnName, $columnDef['precision'][0], $columnDef['precision'][1]);
                                break;
                            case 'enum':
                                $column = $table->enum($columnName, $columnDef['values']);
                                break;
                            default:
                                return;
                        }
                        
                        if (isset($columnDef['nullable']) && $columnDef['nullable']) {
                            $column->nullable();
                        }
                        
                        if (isset($columnDef['default'])) {
                            $column->default($columnDef['default']);
                        }
                        
                        if ($afterColumn) {
                            $column->after($afterColumn);
                        }
                    });
                } catch (\Exception $e) {
                    // Column might already exist or there's an issue, log and continue
                    \Log::warning("Could not add column {$columnName}: " . $e->getMessage());
                }
            }
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
        }
        
        DB::table('sourcings')
            ->where(function($query) {
                $query->whereNull('product_name')
                      ->orWhere('product_name', '');
            })
            ->update(['product_name' => 'Unknown Product']);

        // Step 10: Make country_id not nullable (only if we have a default country) and drop temp columns (if they exist)
        Schema::table('sourcings', function (Blueprint $table) use ($defaultCountryId) {
            // Make country_id not nullable only if there's data with valid country_id
            if ($defaultCountryId) {
                $table->unsignedBigInteger('country_id')->nullable(false)->change();
            }
            
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

        // Step 11: Add foreign key constraints (after ensuring all data is valid) - only if columns and foreign keys don't exist
        $connection = Schema::getConnection();
        $db = $connection->getDatabaseName();
        
        // Get existing foreign keys
        $existingForeignKeys = DB::select("
            SELECT CONSTRAINT_NAME, COLUMN_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'sourcings' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$db]);
        
        $existingKeys = [];
        foreach ($existingForeignKeys as $fk) {
            $existingKeys[$fk->COLUMN_NAME] = $fk->CONSTRAINT_NAME;
        }
        
        // Verify columns exist and add foreign keys
        Schema::table('sourcings', function (Blueprint $table) use ($existingKeys, $db) {
            // Check if category_id column exists and foreign key doesn't exist
            if (Schema::hasColumn('sourcings', 'category_id') && !isset($existingKeys['category_id'])) {
                try {
                    // Verify categories table exists
                    $categoriesExists = DB::select("
                        SELECT COUNT(*) as count 
                        FROM information_schema.tables 
                        WHERE table_schema = ? AND table_name = 'categories'
                    ", [$db]);
                    if (!empty($categoriesExists) && $categoriesExists[0]->count > 0) {
                        $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
                    }
                } catch (\Exception $e) {
                    \Log::warning("Could not add foreign key for category_id: " . $e->getMessage());
                }
            }
            
            // Check if country_id column exists and foreign key doesn't exist
            if (Schema::hasColumn('sourcings', 'country_id') && !isset($existingKeys['country_id'])) {
                try {
                    // Verify countries table exists
                    $countriesExists = DB::select("
                        SELECT COUNT(*) as count 
                        FROM information_schema.tables 
                        WHERE table_schema = ? AND table_name = 'countries'
                    ", [$db]);
                    if (!empty($countriesExists) && $countriesExists[0]->count > 0) {
                        $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
                    }
                } catch (\Exception $e) {
                    \Log::warning("Could not add foreign key for country_id: " . $e->getMessage());
                }
            }
            
            // Check if supplier_id column exists and foreign key doesn't exist
            if (Schema::hasColumn('sourcings', 'supplier_id') && !isset($existingKeys['supplier_id'])) {
                try {
                    // Verify suppliers table exists
                    $suppliersExists = DB::select("
                        SELECT COUNT(*) as count 
                        FROM information_schema.tables 
                        WHERE table_schema = ? AND table_name = 'suppliers'
                    ", [$db]);
                    if (!empty($suppliersExists) && $suppliersExists[0]->count > 0) {
                        $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
                    }
                } catch (\Exception $e) {
                    \Log::warning("Could not add foreign key for supplier_id: " . $e->getMessage());
                }
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
