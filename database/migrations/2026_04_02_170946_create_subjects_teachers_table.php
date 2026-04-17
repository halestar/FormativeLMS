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
        Schema::create('subjects_teachers', function (Blueprint $table)
        {
	        $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
	        $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
	        $table->primary(['person_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects_teachers');
    }
};
