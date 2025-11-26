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
        Schema::table('media_buyer_testing_product', function (Blueprint $table) {
            $table->enum('status', ['to_do', 'in_progress', 'done', 'approved', 'rejected'])->default('to_do')->after('testing_product_id');
            $table->integer('leads')->nullable()->after('status');
            $table->decimal('ads_spend', 12, 2)->nullable()->after('leads');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_buyer_testing_product', function (Blueprint $table) {
            $table->dropColumn(['status', 'leads', 'ads_spend']);
        });
    }
};
