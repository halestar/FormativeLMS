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
		Schema::create('people_relations', function(Blueprint $table)
		{
			$table->bigInteger('from_person_id')
			      ->unsigned();
			$table->foreign('from_person_id')
			      ->references('id')
			      ->on('people')
			      ->onDelete('cascade');
			$table->bigInteger('to_person_id')
			      ->unsigned();
			$table->foreign('to_person_id')
			      ->references('id')
			      ->on('people')
			      ->onDelete('cascade');
			
			$table->bigInteger('relationship_id')
			      ->unsigned()
			      ->nullable();
			$table->foreign('relationship_id')
			      ->references('id')
			      ->on('crud_relationships')
			      ->onDelete('set null');
			$table->primary(['from_person_id', 'to_person_id']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('people_relations');
	}
	};
