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
        Schema::create('skills_courses', function (Blueprint $table) {
	        $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
	        $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
	        $table->primary(['skill_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills_courses');
    }
};
