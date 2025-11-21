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
        Schema::create('learning_demonstration_skills', function (Blueprint $table)
        {
	        $table->uuid('id')->primary();
	        $table->foreignUuid('demonstration_id')->constrained('learning_demonstrations')->cascadeOnDelete();
	        $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
	        $table->json('rubric')->nullable();
	        $table->float('weight')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_demonstration_skills');
    }
};
