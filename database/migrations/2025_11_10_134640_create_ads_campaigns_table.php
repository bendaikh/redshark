<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('platform');
            $table->decimal('amount_spent', 12, 2)->default(0);
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->date('date')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_campaigns');
    }
};

