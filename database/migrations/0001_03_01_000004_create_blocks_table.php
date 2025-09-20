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
		Schema::create('blocks', function(Blueprint $table)
		{
			$table->id();
			$table->foreignId('campus_id');
			$table->foreign('campus_id')
			      ->references('id')
			      ->on('campuses')
			      ->cascadeOnDelete();
			$table->string('name');
			$table->boolean('active')
			      ->default(true);
			$table->tinyInteger('order')
			      ->unsigned()
			      ->default(0);
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('blocks');
	}
	};
