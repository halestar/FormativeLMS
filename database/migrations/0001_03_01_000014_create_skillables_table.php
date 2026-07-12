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
		Schema::create('skillables', function (Blueprint $table)
		{
			$table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
			$table->morphs('skillable');
			$table->unique(['skill_id', 'skillable_id', 'skillable_type']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('skillables');
	}
};
