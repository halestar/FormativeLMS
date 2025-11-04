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
		Schema::create('system_tableable', function(Blueprint $table)
		{
			$table->foreignId('system_table_id')
			      ->constrained('system_tables')
			      ->cascadeOnDelete();
			$table->bigInteger('system_tableable_id');
			$table->string('system_tableable_type');
			$table->primary(['system_table_id', 'system_tableable_id', 'system_tableable_type']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('system_tableable');
	}
	};
