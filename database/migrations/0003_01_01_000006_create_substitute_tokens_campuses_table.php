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
        Schema::create('substitute_tokens_campuses', function (Blueprint $table) {
            $table->string('token');
            $table->foreign('token')->references('token')->on('substitute_tokens')->cascadeOnDelete();
            $table->foreignId('campus_request_id')->constrained('substitute_campus_requests')->cascadeOnDelete();
            $table->primary(['token', 'campus_request_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substitute_tokens_campuses');
    }
};
