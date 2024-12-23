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
        Schema::create('field_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('field')->index();
            $table->foreignId('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->boolean('by_self')->default(false);
            $table->boolean('by_employees')->default(false);
            $table->boolean('by_students')->default(false);
            $table->boolean('by_parents')->default(false);
            $table->boolean('editable')->default(false);
            $table->unique(['field', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_policies');
    }
};
