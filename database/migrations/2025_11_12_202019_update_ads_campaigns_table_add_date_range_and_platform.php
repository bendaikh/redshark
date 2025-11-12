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
        Schema::table('ads_campaigns', function (Blueprint $table) {
            $table->date('date_from')->nullable()->after('date');
            $table->date('date_to')->nullable()->after('date_from');
            $table->foreignId('platform_id')->nullable()->after('platform')->constrained('ads_platforms')->nullOnDelete();
        });
        
        // Migrate existing data if any
        DB::statement('UPDATE ads_campaigns SET date_from = date, date_to = date WHERE date_from IS NULL');
        
        Schema::table('ads_campaigns', function (Blueprint $table) {
            $table->dropColumn(['date', 'amount_spent', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads_campaigns', function (Blueprint $table) {
            $table->string('platform')->after('platform_id');
            $table->decimal('amount_spent', 12, 2)->default(0)->after('platform');
            $table->date('date')->after('country_id');
        });
        
        Schema::table('ads_campaigns', function (Blueprint $table) {
            $table->dropForeign(['platform_id']);
            $table->dropColumn(['date_from', 'date_to', 'platform_id']);
        });
    }
};
