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
        Schema::create('learning_demonstration_templates', function (Blueprint $table) {
	        $table->uuid('id')->primary();
	        $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
			$table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
			$table->foreignId('type_id')->constrained('learning_demonstration_types')->cascadeOnDelete();
	        $table->string('name');
	        $table->string('abbr', 10);
	        $table->text('demonstration')->nullable();
	        $table->json('links')->nullable();
	        $table->json('questions')->nullable();
			$table->boolean('allow_rating')->default(false);
	        $table->boolean('online_submission')->default(true);
	        $table->boolean('open_submission')->default(false);
	        $table->boolean('submit_after_due')->default(false);
	        $table->boolean('share_submissions')->default(false);
			$table->foreignUuid('created_by')->nullable()->constrained('learning_demonstration_templates')->nullOnDelete();
	        $table->boolean('shareable')->default(false);
	        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_demonstration_templates');
    }
};
