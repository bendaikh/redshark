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
        Schema::table('products', function (Blueprint $table) {
            // Drop selling_price column
            $table->dropColumn('selling_price');
            
            // Drop old category column
            $table->dropColumn('category');
            
            // Add category_id foreign key
            $table->foreignId('category_id')->nullable()->after('name')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop category_id
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            
            // Add back category and selling_price
            $table->string('category')->nullable()->after('name');
            $table->decimal('selling_price', 12, 2)->default(0)->after('cost');
        });
    }
};
