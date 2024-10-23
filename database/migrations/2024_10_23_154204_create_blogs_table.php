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
        Schema::create(config('dicms.table_prefix') . 'blogs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('slug');
            $table->bigInteger('index_id')->unsigned()->nullable();
            $table->foreign('index_id')
                ->references('id')
                ->on(config('dicms.table_prefix') . 'pages')
                ->onDelete('set null');
            $table->bigInteger('post_id')->unsigned()->nullable();
            $table->foreign('post_id')
                ->references('id')
                ->on(config('dicms.table_prefix') . 'pages')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
