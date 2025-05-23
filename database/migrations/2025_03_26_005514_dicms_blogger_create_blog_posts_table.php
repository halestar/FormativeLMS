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
        Schema::create(config('dicms.table_prefix') . 'blog_posts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('blog_id')->unsigned();
            $table->foreign('blog_id')
                ->references('id')
                ->on(config('dicms.table_prefix') . 'blogs')
                ->onDelete('cascade');
            $table->string('title')->fulltext();
            $table->string('subtitle')->nullable()->fulltext();
            $table->string('slug');
            $table->string('posted_by')->fulltext();
            $table->text('description')->nullable()->fulltext();
            $table->longText('body')->nullable()->fulltext();
            $table->dateTime('published')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('highlighted')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('dicms.table_prefix') . 'blog_posts');
    }
};
