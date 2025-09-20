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
		Schema::create('blocks_periods', function(Blueprint $table)
		{
			$table->foreignId('block_id');
			$table->foreign('block_id')
			      ->references('id')
			      ->on('blocks')
			      ->cascadeOnDelete();
			$table->foreignId('period_id');
			$table->foreign('period_id')
			      ->references('id')
			      ->on('periods')
			      ->cascadeOnDelete();
			$table->primary(['block_id', 'period_id']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('blocks_periods');
	}
	};
