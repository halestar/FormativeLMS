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
        Schema::create('knowledge_skill_levels', function (Blueprint $table) {
            $table->foreignId('skill_id')->constrained('knowledge_skills')->onDelete('cascade');
            $table->foreignId('level_id')->constrained('crud_levels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_skill_levels');
    }
};
