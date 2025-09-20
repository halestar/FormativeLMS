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
		Schema::create('subjects', function(Blueprint $table)
		{
			$table->id();
			$table->foreignId('campus_id');
			$table->foreign('campus_id')
			      ->references('id')
			      ->on('campuses')
			      ->onDelete('cascade');
			$table->string('name');
			$table->string('color')
			      ->default('#000000');
			$table->integer('required_terms')
			      ->nullable();
			$table->tinyInteger('order')
			      ->unsigned()
			      ->default(1);
			$table->boolean('active')
			      ->default(true);
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('subjects');
	}
	};
