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
		Schema::create('work_files', function(Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('storage_instance');
			$table->string('path');
			$table->string('mime');
			$table->string('size');
			$table->string('extension');
			$table->string('url')
			      ->nullable();
			$table->string('icon')
			      ->nullable();
			$table->timestamps();
			$table->unique(['storage_instance', 'path']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('work_files');
	}
	};
