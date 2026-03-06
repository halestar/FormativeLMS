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
        Schema::create('substitute_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->nullable()->constrained('people')->nullOnDelete();
            $table->string('requester_name');
            $table->date('requested_for');
            $table->boolean('completed')->default(false);
            $table->boolean('internal')->default(false);
            $table->unique(['requester_id', 'requester_name', 'requested_for'], 'unique_substitute_request');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substitute_requests');
    }
};
