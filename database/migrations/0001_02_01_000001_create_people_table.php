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
		Schema::create('people', function(Blueprint $table)
		{
			$table->id();
			
			$table->string('first')
			      ->nullable();
			$table->string('middle')
			      ->nullable();
			$table->string('last');
			
			$table->string('email')
			      ->nullable();
			$table->string('nick')
			      ->nullable();
			$table->date('dob')
			      ->nullable();
			
			$table->string('portrait_url')
			      ->nullable();
			$table->json('prefs')
			      ->nullable();
			$table->bigInteger('school_id')
			      ->unsigned()
			      ->unique()
			      ->index();
			$table->uuid('auth_connection_id')
			      ->nullable();
			$table->fullText(['first', 'middle', 'last', 'email', 'nick'], 'search_index');
			$table->rememberToken();
			$table->softDeletes();
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('people');
	}
	};
