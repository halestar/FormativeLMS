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
        Schema::create('buildings_areas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('building_id')->unsigned();
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
            $table->bigInteger('area_id')->unsigned();
            $table->foreign('area_id')->references('id')->on('crud_school_areas')->onDelete('cascade');
            $table->string('blueprint_url')->nullable();
            $table->string('img')->nullable();
            $table->tinyInteger('order')->unsigned()->default(1);
            $table->unique(['building_id', 'area_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings_areas');
    }
};
