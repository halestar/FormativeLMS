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
        Schema::create('people_addresses', function (Blueprint $table) {
            $table->bigInteger('person_id')->unsigned();
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            $table->bigInteger('address_id')->unsigned();
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->boolean('primary')->default(true);
            $table->boolean('work')->default(false);
            $table->boolean('seasonal')->default(false);
            $table->date('season_start')->nullable();
            $table->date('season_end')->nullable();
            $table->primary(['person_id', 'address_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people_addresses');
    }
};
