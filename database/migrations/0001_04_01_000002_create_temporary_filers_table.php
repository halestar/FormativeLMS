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
        Schema::create('temporary_filers', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
			$table->enum('storage_type', \App\Enums\WorkStoragesInstances::cases());
			$table->unique(['person_id', 'storage_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_filers');
    }
};
