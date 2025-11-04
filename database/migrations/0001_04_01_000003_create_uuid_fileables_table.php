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
		Schema::create('uuid_fileables', function(Blueprint $table)
		{
			$table->uuid('work_file_id');
			$table->foreign('work_file_id')
			      ->references('id')
			      ->on('work_files')
			      ->cascadeOnDelete();
			$table->uuid('fileable_id');
			$table->string('fileable_type');
			$table->primary(['work_file_id', 'fileable_id', 'fileable_type']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('uuid_fileables');
	}
	};
