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
		Schema::create('fileables', function(Blueprint $table)
		{
			$table->uuid('work_file_id');
			$table->foreign('work_file_id')
			      ->references('id')
			      ->on('work_files')
			      ->cascadeOnDelete();
			$table->bigInteger('fileable_id')->nullable();
			$table->uuid('fileable_uuid')->nullable();
			$table->string('fileable_type');
			$table->unique(['work_file_id', 'fileable_id', 'fileable_uuid', 'fileable_type'], 'fileable_unique');
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
