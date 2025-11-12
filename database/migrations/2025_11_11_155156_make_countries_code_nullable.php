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
        Schema::table('countries', function (Blueprint $table) {
            // Drop the unique constraint on code
            $table->dropUnique(['code']);
            
            // Make code nullable
            $table->string('code', 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            // Make code not nullable again
            $table->string('code', 3)->nullable(false)->change();
            
            // Add back the unique constraint
            $table->unique('code');
        });
    }
};
