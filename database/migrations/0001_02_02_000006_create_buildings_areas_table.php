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
		Schema::create('buildings_areas', function(Blueprint $table)
		{
			$table->id();
			$table->foreignId('building_id')
			      ->constrained('buildings')
				  ->cascadeOnDelete();
			$table->foreignId('area_id')
			      ->constrained('system_tables')
				  ->cascadeOnDelete();
			$table->string('blueprint_url')
			      ->nullable();
			$table->unique(['building_id', 'area_id']);
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('buildings_areas');
	}
	};
