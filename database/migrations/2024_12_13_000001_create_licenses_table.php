<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LICENSE SERVER MIGRATION
 * 
 * Only run this migration on YOUR license server, not on client installations.
 * This table stores all the licenses you've issued to clients.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key')->unique();
            $table->string('product_id');
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('domain')->nullable();
            $table->string('type')->default('STD'); // STD, PRO, ENT
            $table->string('status')->default('pending'); // pending, active, suspended, expired, revoked
            $table->integer('max_activations')->default(1);
            $table->json('features')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_validated_at')->nullable();
            $table->string('last_validated_ip')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index('domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};

