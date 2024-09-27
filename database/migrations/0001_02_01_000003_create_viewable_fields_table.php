<?php

use App\Enums\PolicyType;
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
        Schema::create('viewable_fields', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('crud_viewable_groups')->onDelete('cascade');
            $table->string('name');
            $table->string('field');
            $table->string('parent_class');
            $table->boolean('format_as_date')->default(false);
            $table->boolean('format_as_datetime')->default(false);
            $table->tinyInteger('order')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viewable_fields');
    }
};
