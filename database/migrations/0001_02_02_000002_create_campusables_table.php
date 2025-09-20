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
		Schema::create('campusables', function(Blueprint $table)
		{
			$table->foreignId('campus_id')
			      ->constrained('campuses')
			      ->cascadeOnDelete();
			$table->bigInteger('campusable_id');
			$table->string('campusable_type');
			$table->primary(['campus_id', 'campusable_id', 'campusable_type']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('campusable');
	}
	};
