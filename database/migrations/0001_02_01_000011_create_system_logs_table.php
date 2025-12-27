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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->string('type');
			$table->text('message');
			$table->foreignId('posted_by')->nullable()->constrained('people')->nullOnDelete();
			$table->string('posted_by_name');
			$table->string('posted_by_email');
			$table->uuid('loggable_uuid')->nullable();
			$table->unsignedBigInteger('loggable_id')->nullable();
			$table->string('loggable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
