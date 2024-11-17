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
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbr', 10);
            $table->string('title')->nullable();
            $table->date('established')->nullable();
            $table->tinyInteger('order')->unsigned()->default(1);
            $table->string('img')->nullable();
            $table->text('icon')->nullable();
            $table->string('color_pri')->default('#000000');
            $table->string('color_sec')->default('#ffffff');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campuses');
    }
};
