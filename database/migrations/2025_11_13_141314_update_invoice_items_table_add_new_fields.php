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
        // Add date range to invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('date_from')->nullable()->after('date');
            $table->date('date_to')->nullable()->after('date_from');
        });

        // Add new fields to invoice_items table
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('revenue', 12, 2)->default(0)->after('total_cost');
            $table->unsignedInteger('total_orders')->default(0)->after('revenue');
            $table->unsignedInteger('quantity_sold')->default(0)->after('total_orders');
            $table->foreignId('delivery_fee_id')->nullable()->constrained('delivery_fees')->nullOnDelete()->after('quantity_sold');
            $table->decimal('ads_cost', 12, 2)->default(0)->after('delivery_fee_id');
            $table->decimal('net_profit', 12, 2)->default(0)->after('ads_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['delivery_fee_id']);
            $table->dropColumn(['revenue', 'total_orders', 'quantity_sold', 'delivery_fee_id', 'ads_cost', 'net_profit']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['date_from', 'date_to']);
        });
    }
};
