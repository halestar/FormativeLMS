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
        Schema::create('substitute_tokens', function (Blueprint $table) {
            $table->string('token')->primary();
            $table->foreignId('substitute_id')->constrained('substitutes', 'person_id')->cascadeOnDelete();
            $table->foreignId('request_id')->nullable()->constrained('substitute_requests')->cascadeOnDelete();
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substitute_tokens');
    }
};
