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
        Schema::create('integrators', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('className');
	        $table->string('path')->index();
			$table->string('description')->nullable();
			$table->json('data')->nullable();
			$table->string('version')->nullable();
			$table->boolean('enabled')->default(true);
			$table->boolean('has_personal_connections')->default(false);
			$table->boolean('has_system_connections')->default(false);
			$table->boolean('configurable')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrators');
    }
};
