<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('ads_campaigns', function (Blueprint $table) {
			$table->decimal('amount_spent', 12, 2)->nullable()->after('name');
			$table->unsignedInteger('leads')->nullable()->after('amount_spent');
		});
	}

	public function down(): void
	{
		Schema::table('ads_campaigns', function (Blueprint $table) {
			$table->dropColumn(['amount_spent', 'leads']);
		});
	}
};
