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
		Schema::create(config('dicms.table_prefix') . 'related_posts', function(Blueprint $table)
		{
			$table->unsignedBigInteger('post_id');
			$table->foreign('post_id')
			      ->references('id')
			      ->on(config('dicms.table_prefix') . 'blog_posts')
			      ->onDelete('cascade');
			$table->unsignedBigInteger('related_post_id');
			$table->foreign('related_post_id')
			      ->references('id')
			      ->on(config('dicms.table_prefix') . 'blog_posts')
			      ->onDelete('cascade');
		});
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('related_posts');
	}
	};
