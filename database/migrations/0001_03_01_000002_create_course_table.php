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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('description')->nullable();
            $table->float('credits')->default(0);
            $table->boolean('on_transcript')->default(true);
            $table->boolean('gb_required')->default(true);
            $table->boolean('honors')->default(false);
            $table->boolean('ap')->default(false);
            $table->boolean('can_assign_honors')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
