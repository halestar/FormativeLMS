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
		Schema::create('ai_prompts', function(Blueprint $table)
		{
			$table->id();
			$table->foreignId('person_id')
			      ->constrained()
			      ->cascadeOnDelete();
			$table->string('className');
			$table->string('property');
			$table->text('prompt')
			      ->nullable();
			$table->text('system_prompt')
			      ->nullable();
			$table->boolean('structured')
			      ->nullable();
			$table->float('temperature')
			      ->default(0);
			$table->json('last_results')
			      ->nullable();
			$table->string('last_id')->nullable();
			$table->unique(['className', 'property', 'person_id']);
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('ai_prompts');
	}
	};
