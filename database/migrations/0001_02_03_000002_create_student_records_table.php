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
		Schema::create('student_records', function(Blueprint $table)
		{
			$table->id();
			$table->foreignId('campus_id');
			$table->foreign('campus_id')
			      ->references('id')
			      ->on('campuses')
			      ->onDelete('cascade');
			$table->foreignId('person_id');
			$table->foreign('person_id')
			      ->references('id')
			      ->on('people')
			      ->onDelete('cascade');
			$table->foreignId('year_id');
			$table->foreign('year_id')
			      ->references('id')
			      ->on('years')
			      ->onDelete('cascade');
			$table->foreignId('level_id');
			$table->foreign('level_id')
			      ->references('id')
			      ->on('system_tables')
			      ->onDelete('cascade');
			$table->date('start_date');
			$table->date('end_date')
			      ->nullable();
			$table->foreignId('dismissal_reason_id')
			      ->nullable();
			$table->foreign('dismissal_reason_id')
			      ->references('id')
			      ->on('system_tables')
			      ->onDelete('set null');
			$table->text('dismissal_note')
			      ->nullable();
			$table->unique(['campus_id', 'person_id', 'year_id', 'level_id']);
			$table->timestamps();
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('student_records');
	}
	};
