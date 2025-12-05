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
        Schema::create('class_communication_objects', function (Blueprint $table)
        {
            $table->uuid('id')->primary();
            $table->foreignId('session_id')->constrained('class_sessions')->cascadeOnDelete();
            $table->string('className');
            $table->json('value')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('people')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_communication_objects');
    }
};
