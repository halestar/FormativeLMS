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
        Schema::create('phoneables', function (Blueprint $table) {
            $table->bigInteger('phone_id')->unsigned();
            $table->foreign('phone_id')->references('id')->on('phones')->onDelete('cascade');
            $table->bigInteger('phoneable_id')->unsigned()->index();
            $table->string('phoneable_type');
            $table->boolean('primary')->default(true);
            $table->string('label')->nullable();
            $table->tinyInteger('order')->unsigned()->default(1);
            $table->primary(['phone_id', 'phoneable_id', 'phoneable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phoneables');
    }
};
