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
		Schema::create('skills', function(Blueprint $table)
		{
			$table->id();
			$table->string('designation');
			$table->string('name')
			      ->nullable();
			$table->text('description')
			      ->nullable();
			$table->json('rubric')
			      ->nullable();
			$table->boolean('global')
			      ->default(false);
			$table->boolean('active')
			      ->default(false);
			$table->fullText(['designation', 'name', 'description'], 'search_index');
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('skills');
	}
	};
