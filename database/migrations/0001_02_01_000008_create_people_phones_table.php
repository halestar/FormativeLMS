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
        Schema::create('people_phones', function (Blueprint $table) {
            $table->bigInteger('person_id')->unsigned();
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            $table->bigInteger('phone_id')->unsigned();
            $table->foreign('phone_id')->references('id')->on('phones')->onDelete('cascade');
            $table->boolean('primary')->default(true);
            $table->boolean('work')->default(false);
            $table->primary(['person_id', 'phone_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people_phones');
    }
};
