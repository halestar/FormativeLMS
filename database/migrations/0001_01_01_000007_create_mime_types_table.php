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
		Schema::create('mime_types', function(Blueprint $table)
		{
			$table->string('mime')
			      ->primary();
			$table->string('extension');
			$table->text('icon')
			      ->nullable();
			$table->boolean('is_img')
			      ->default(false);
			$table->boolean('is_video')
				->default(false);
			$table->boolean('is_audio')
				->default(false);
			$table->boolean('is_document')
				->default(false);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('mime_types');
	}
	};
