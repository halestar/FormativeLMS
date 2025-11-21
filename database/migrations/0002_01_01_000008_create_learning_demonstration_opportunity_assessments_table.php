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
        Schema::create('learning_demonstration_opportunity_assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->foreignUuid('opportunity_id')->constrained('learning_demonstration_opportunities', 'id', 'ld_opportunity_fk')->cascadeOnDelete();
			$table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
	        $table->json('rubric')->nullable();
	        $table->float('weight')->default(1);
			$table->float('score')->nullable();
			$table->text('feedback')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_demonstration_opportunity_assessments');
    }
};
