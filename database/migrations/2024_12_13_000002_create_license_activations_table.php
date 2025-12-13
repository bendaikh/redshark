<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LICENSE SERVER MIGRATION
 * 
 * Only run this migration on YOUR license server, not on client installations.
 * This table tracks license activations across multiple servers.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('license_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->onDelete('cascade');
            $table->string('server_id');
            $table->string('domain');
            $table->string('ip_address')->nullable();
            $table->string('hostname')->nullable();
            $table->timestamp('activated_at');
            $table->timestamp('last_seen_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['license_id', 'server_id']);
            $table->index('server_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_activations');
    }
};

