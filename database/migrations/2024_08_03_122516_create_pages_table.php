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
        Schema::create(config('dicms.table_prefix') . 'pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->foreign('site_id')
                ->references('id')
                ->on(config('dicms.table_prefix') . 'sites')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('title')->nullable();
            $table->string('path')->nullable();
            $table->string('url')->unique();
            $table->unsignedBigInteger('header_id')->nullable();
            $table->foreign('header_id')->references('id')->on(config('dicms.table_prefix') . 'headers')->onDelete('set null');
            $table->unsignedBigInteger('footer_id')->nullable();
            $table->foreign('footer_id')->references('id')->on(config('dicms.table_prefix') . 'footers')->onDelete('set null');
            $table->longText('html')->nullable();
            $table->longText('css')->nullable();
            $table->json('data')->nullable();
            $table->boolean('published')->default(false);
            $table->unique(['slug', 'path']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('dicms.table_prefix') . 'pages');
    }
};
