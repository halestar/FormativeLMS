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
        Schema::create('grade_translation_schemas', function (Blueprint $table) {
            $table->id();
			$table->foreignId('campus_id')->constrained('campuses')->cascadeOnDelete();
			$table->foreignId('year_id')->constrained('years')->cascadeOnDelete();
			$table->boolean('show_opportunity_grade')->default(true);
			$table->boolean('translate_opportunity_grade')->default(false);
			$table->boolean('show_criteria_grade')->default(true);
			$table->boolean('translate_criteria_grade')->default(false);
			$table->boolean('show_overall_grade')->default(true);
			$table->boolean('translate_overall_grade')->default(false);
			$table->json('grade_translations')->nullable();
			$table->unique(['campus_id', 'year_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_translation_schemas');
    }
};
