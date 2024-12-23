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
        Schema::create('class_sessions_students', function (Blueprint $table) {
            $table->foreignId('session_id')->constrained('class_sessions')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('student_records')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions_students');
    }
};
