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
        Schema::create('substitute_campus_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('substitute_requests')->cascadeOnDelete();
            $table->foreignId('campus_id')->constrained('campuses')->cascadeOnDelete();
            $table->foreignId('substitute_id')->nullable()->constrained('substitutes', 'person_id')->nullOnDelete();
            $table->dateTime('responded_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substitute_campus_requests');
    }
};
