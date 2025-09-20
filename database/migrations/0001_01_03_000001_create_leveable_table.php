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
		Schema::create('leveables', function(Blueprint $table)
		{
			$table->foreignId('level_id')
			      ->constrained('crud_levels')
			      ->cascadeOnDelete();
			$table->bigInteger('leveable_id');
			$table->string('leveable_type');
			$table->primary(['level_id', 'leveable_id', 'leveable_type']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('leveable');
	}
	};
