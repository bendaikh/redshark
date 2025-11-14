<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sourcings', function (Blueprint $table) {
            // Drop foreign key constraint and product_id column
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');

            // Add new fields
            $table->string('product_name')->after('id');
            $table->string('product_image')->nullable()->after('product_name');
            $table->foreignId('category_id')->nullable()->after('product_image')->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity')->default(0)->after('category_id');
            $table->foreignId('country_id')->after('quantity')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 2)->default(0)->after('country_id');
            $table->decimal('cost', 12, 2)->default(0)->after('price');
            $table->enum('shipping_type', ['in_transit', 'arrived'])->nullable()->after('cost');
            $table->decimal('additional_fees', 12, 2)->default(0)->after('shipping_type');
            $table->decimal('testing_fees', 12, 2)->default(0)->after('additional_fees');
            $table->foreignId('supplier_id')->nullable()->after('testing_fees')->constrained()->nullOnDelete();
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
