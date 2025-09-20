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
			$table->foreignId('system_prompt_id')
			      ->constrained('ai_system_prompts')
			      ->cascadeOnDelete();
			$table->foreignId('person_id')
			      ->nullable()
			      ->constrained()
			      ->cascadeOnDelete();
			$table->morphs('ai_promptable');
			$table->text('prompt')
			      ->nullable();
			$table->boolean('structured')
			      ->nullable();
			$table->float('temperature')
			      ->default(0);
			$table->json('tools')
			      ->nullable();
			$table->json('last_results')
			      ->nullable();
			$table->unique(['ai_promptable_id', 'ai_promptable_type', 'person_id']);
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
