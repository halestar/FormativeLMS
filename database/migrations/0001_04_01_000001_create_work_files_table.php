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
		Schema::create('work_files', function(Blueprint $table)
		{
			$table->uuid('id')
			      ->primary();
			$table->string('name');
			$table->uuid('connection_id')
			      ->nullable();
			$table->foreign('connection_id')
			      ->references('id')
			      ->on('integration_connections')
			      ->onDelete('set null');
			$table->string('path');
			$table->string('mime');
			$table->string('size');
			$table->string('extension');
			$table->string('url')
			      ->nullable();
			$table->string('icon')
			      ->nullable();
			$table->boolean('invisible')
			      ->default(false);
			$table->boolean('public')
			      ->default(false);
			$table->timestamps();
			$table->unique(['connection_id', 'path']);
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
