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
		Schema::create('system_tables', function(Blueprint $table)
		{
			$table->id();
			$table->string('name');
			$table->tinyInteger('order')
			      ->unsigned()
			      ->default(0);
			$table->string('className');
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('system_tables');
	}
	};
