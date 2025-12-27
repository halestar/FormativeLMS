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
		Schema::create('rooms', function(Blueprint $table)
		{
			$table->id();
			$table->foreignId('area_id')
				  ->nullable()
				  ->constrained('buildings_areas')
				  ->cascadeOnDelete();
			$table->string('name');
			$table->integer('capacity')
			      ->unsigned()
			      ->default(50);
			$table->text('img_data')
			      ->nullable();
			$table->foreignId('phone_id')
			      ->nullable()
			      ->constrained('phones')
			      ->cascadeOnDelete();
			$table->text('notes')
			      ->nullable();
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('rooms');
	}
	};
