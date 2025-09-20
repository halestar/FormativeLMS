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
		Schema::create('campuses_rooms', function(Blueprint $table)
		{
			$table->bigInteger('campus_id')
			      ->unsigned();
			$table->foreign('campus_id')
			      ->references('id')
			      ->on('campuses')
			      ->onDelete('cascade');
			$table->bigInteger('room_id')
			      ->unsigned();
			$table->foreign('room_id')
			      ->references('id')
			      ->on('rooms')
			      ->onDelete('cascade');
			$table->string('label')
			      ->nullable();
			$table->boolean('classroom')
			      ->default(true);
			$table->primary(['campus_id', 'room_id']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('campuses_rooms');
	}
	};
