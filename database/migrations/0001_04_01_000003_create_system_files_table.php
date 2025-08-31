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
		Schema::create('system_files', function(Blueprint $table)
		{
			$table->string('name');
			$table->foreign('name')
			      ->references('name')
			      ->on('system_settings')
			      ->cascadeOnDelete();
			$table->uuid('work_file_id');
			$table->foreign('work_file_id')
			      ->references('id')
			      ->on('work_files')
			      ->cascadeOnDelete();
			$table->primary(['name', 'work_file_id']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('system_files');
	}
	};
