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
			$table->bigInteger('area_id')
			      ->unsigned()
			      ->nullable();
			$table->foreign('area_id')
			      ->references('id')
			      ->on('buildings_areas')
			      ->onDelete('set null');
			$table->string('name');
			$table->integer('capacity')
			      ->unsigned()
			      ->default(50);
			$table->text('img_data')
			      ->nullable();
			$table->bigInteger('phone_id')
			      ->unsigned()
			      ->nullable();
			$table->foreign('phone_id')
			      ->references('id')
			      ->on('phones')
			      ->onDelete('set null');
			$table->text('notes')
			      ->nullable();
			$table->boolean('classroom')
			      ->default(false);
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
