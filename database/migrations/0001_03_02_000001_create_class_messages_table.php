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
        Schema::create('class_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')
                ->references('id')
                ->on('class_sessions')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('student_id');
            $table->foreign('student_id')
                ->references('id')
                ->on('student_records')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('person_id')->nullable();
            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->nullOnDelete();
            $table->text('message');
            $table->tinyInteger('from_type')->unsigned()->default(0);

            $table->index(['session_id', 'student_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_messages');
    }
};
