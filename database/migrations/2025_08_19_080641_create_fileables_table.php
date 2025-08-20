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
		Schema::create('fileables', function(Blueprint $table) {
			$table->foreignId('work_file_id')
			      ->constrained('work_files')
			      ->cascadeOnDelete();
			$table->bigInteger('fileable_id');
			$table->string('fileable_type');
			$table->primary(['work_file_id', 'fileable_id', 'fileable_type']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('fileables');
	}
	};
