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
        Schema::create('view_policies_fields', function (Blueprint $table) {
            $table->bigInteger('policy_id')->unsigned();
            $table->foreign('policy_id')->references('id')->on('view_policies')->onDelete('cascade');
            $table->bigInteger('field_id')->unsigned();
            $table->foreign('field_id')->references('id')->on('viewable_fields')->onDelete('cascade');
            $table->boolean('editable')->default(false);
            $table->boolean('self_viewable')->default(false);
            $table->boolean('employee_viewable')->default(false);
            $table->boolean('employee_enforce')->default(false);
            $table->boolean('parent_viewable')->default(false);
            $table->boolean('parent_enforce')->default(false);
            $table->boolean('student_viewable')->default(false);
            $table->boolean('student_enforce')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_policies_fields');
    }
};
