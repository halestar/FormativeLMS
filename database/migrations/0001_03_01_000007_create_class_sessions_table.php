<?php

use App\Enums\AssessmentStrategyCalculationMethod;
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
		Schema::create('class_sessions', function(Blueprint $table)
		{
			$table->id();
			$table->foreignId('class_id');
			$table->foreign('class_id')
			      ->references('id')
			      ->on('school_classes')
			      ->cascadeOnDelete();
			$table->foreignId('term_id');
			$table->foreign('term_id')
			      ->references('id')
			      ->on('terms')
			      ->cascadeOnDelete();
			$table->foreignId('room_id')
			      ->nullable();
			$table->foreign('room_id')
			      ->references('id')
			      ->on('rooms')
			      ->nullOnDelete();
			$table->foreignId('block_id')
			      ->nullable();
			$table->foreign('block_id')
			      ->references('id')
			      ->on('blocks')
			      ->nullOnDelete();
			$table->uuid('class_management_id')
				->nullable();
			$table->foreign('class_management_id')
				->references('id')
				->on('integration_connections')
				->nullOnDelete();
			$table->json('layout')
			      ->nullable();
			$table->boolean('setup_completed')->default(false);
			$table->boolean('inherit_criteria')->default(true);
			$table->string('calc_method')->default(AssessmentStrategyCalculationMethod::Percent->value);
			
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('class_sessions');
	}
};
