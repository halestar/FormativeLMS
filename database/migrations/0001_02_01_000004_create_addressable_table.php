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
        Schema::create('addressable', function (Blueprint $table) {
            $table->bigInteger('address_id')->unsigned();
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->bigInteger('addressable_id')->unsigned()->index();
            $table->string('addressable_type');
            $table->boolean('primary')->default(true);
            $table->string('label')->nullable();
            $table->tinyInteger('order')->unsigned()->default(1);
            $table->primary(['address_id', 'addressable_id', 'addressable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addressable');
    }
};
