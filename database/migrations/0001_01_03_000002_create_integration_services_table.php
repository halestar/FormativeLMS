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
		Schema::create('integration_services', function(Blueprint $table)
		{
			$table->id();
			$table->bigInteger('integrator_id')
			      ->unsigned();
			$table->foreign('integrator_id')
			      ->references('id')
			      ->on('integrators')
			      ->onDelete('cascade');
			$table->string('name');
			$table->string('className');
			$table->string('path')
			      ->index();
			$table->string('description')
			      ->nullable();
			$table->string('service_type')
			      ->index();
			$table->json('data')
			      ->nullable();
			$table->boolean('enabled')
			      ->default(true);
			$table->boolean('can_connect_to_people')
			      ->default(false);
			$table->boolean('can_connect_to_system')
			      ->default(false);
			$table->boolean('configurable')
			      ->default(false);
			$table->boolean('inherit_permissions')
			      ->default(true);
			$table->unique(['integrator_id', 'service_type']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('integration_services');
	}
	};
