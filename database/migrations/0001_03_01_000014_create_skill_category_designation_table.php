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
        Schema::create('skill_category_designation', function (Blueprint $table)
        {
            $table->foreignId('category_id')->constrained('skill_categories')->cascadeOnDelete();
            $table->foreignId('designation_id')->constrained('crud_skill_category_designations')->cascadeOnDelete();
            $table->bigInteger('skill_id')->unsigned();
            $table->string('skill_type');
            $table->primary(['category_id', 'skill_id', 'skill_type', 'designation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_category_designation');
    }
};
