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
        Schema::create('testing_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->text('product_link');
            $table->text('facebook_library_link')->nullable();
            $table->json('country_ids')->nullable();
            $table->text('video_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testing_products');
    }
};
