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
        Schema::create('learning_demonstration_opportunities', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->foreignUuid('demonstration_session_id')->constrained('learning_demonstration_class_sessions', 'id', 'demonstration_session_fk')->cascadeOnDelete();
			$table->foreignId('student_id')->constrained('student_records')->cascadeOnDelete();
	        $table->dateTime('posted_on');
	        $table->dateTime('due_on');
			$table->boolean('completed')->default(false);
			$table->dateTime('submitted_on')->nullable();
			$table->text('feedback')->nullable();
			$table->float('score')->nullable();
			$table->float('criteria_weight')->default(1.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_demonstration_opportunities');
    }
};
