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
		Schema::create('periods', function(Blueprint $table)
		{
			$table->id();
			$table->foreignId('campus_id');
			$table->foreign('campus_id')
			      ->references('id')
			      ->on('campuses')
			      ->cascadeOnDelete();
			$table->tinyInteger('day')
			      ->unsigned();
			$table->string('name');
			$table->string('abbr');
			$table->time('start');
			$table->time('end');
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
		Schema::dropIfExists('periods');
	}
	};
