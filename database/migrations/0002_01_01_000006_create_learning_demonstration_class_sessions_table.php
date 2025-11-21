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
        Schema::create('learning_demonstration_class_sessions', function (Blueprint $table) {
			$table->uuid('id')->primary();
	        $table->foreignUuid('demonstration_id')->constrained('learning_demonstrations')->cascadeOnDelete();
			$table->foreignId('session_id')->constrained('class_sessions')->cascadeOnDelete();
			$table->foreignId('criteria_id')->constrained('class_criteria')->cascadeOnDelete();
	        $table->float('criteria_weight')->default(1);
	        $table->unique(['demonstration_id', 'session_id'], 'ls_session_demonstration_unique');
			$table->dateTime('posted_on');
			$table->dateTime('due_on');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_demonstration_class_sessions');
    }
};
