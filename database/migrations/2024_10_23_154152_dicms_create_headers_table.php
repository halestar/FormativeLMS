<?php

use halestar\LaravelDropInCms\Enums\HeaderTagType;
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
        Schema::create(config('dicms.table_prefix') . 'headers', function (Blueprint $table) {
            $table->id();$table->string('name');
            $table->string('description')->nullable();
            $table->longText('html')->nullable();
            $table->longText('css')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('dicms.table_prefix') . 'headers');
    }
};