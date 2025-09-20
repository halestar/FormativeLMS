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
        Schema::create('integration_connections', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->bigInteger('service_id')->unsigned();
			$table->foreign('service_id')->references('id')->on('integration_services')->onDelete('cascade');
			$table->bigInteger('person_id')->unsigned()->nullable();
			$table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
			$table->json('data')->nullable();
			$table->string('className');
			$table->boolean('enabled')->default(true);
			$table->unique(['service_id', 'person_id']);
        });
		Schema::table('people', function (Blueprint $table)
		{
			$table->foreign('auth_connection_id', 'auth_connection_id_FK')->references('id')->on('integration_connections')->onDelete('set null');
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
	    Schema::table('people', function (Blueprint $table)
	    {
			$table->dropForeign(['auth_connection_id_FK']);
		    $table->foreign('auth_connection_id')->references('id')->on('integration_connections')->onDelete('set null');
	    });
        Schema::dropIfExists('integration_connections');
    }
};
