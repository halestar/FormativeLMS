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
        Schema::create('llms', function (Blueprint $table) {
            $table->id();
			$table->foreignUuid('connection_id')->constrained('integration_connections')->cascadeOnDelete();
			$table->string('model_id');
			$table->string('name');
			$table->string('description')->nullable();
			$table->boolean('hide')->default(false);
			$table->smallInteger('order')->default(1);
			$table->json('provider_options')->nullable();
            $table->timestamps();
			$table->unique(['connection_id', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llms');
    }
};
