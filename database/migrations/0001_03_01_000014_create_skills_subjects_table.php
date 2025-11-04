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
        Schema::create('skills_subjects', function (Blueprint $table) {
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
			$table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->primary(['skill_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills_subjects');
    }
};
