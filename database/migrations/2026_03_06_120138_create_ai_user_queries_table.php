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
        Schema::create('ai_user_queries', function (Blueprint $table) {
            $table->id();
			$table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
			$table->string('connection_info');
			$table->string('llm');
			$table->text('prompt');
	        $table->text('system_prompt');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_user_queries');
    }
};
