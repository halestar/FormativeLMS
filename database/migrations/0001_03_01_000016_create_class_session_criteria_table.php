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
        Schema::create('class_session_criteria', function (Blueprint $table) {
            $table->foreignId('criteria_id')->constrained('class_criteria')->cascadeOnDelete();
			$table->foreignId('session_id')->constrained('class_sessions')->cascadeOnDelete();
			$table->float('weight')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_session_criteria');
    }
};
