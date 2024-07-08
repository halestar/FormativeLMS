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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('first')->nullable();
            $table->string('middle')->nullable();
            $table->string('last');
            $table->string('email')->nullable();
            $table->string('nick')->nullable();
            $table->string('pronouns')->nullable();
            $table->date('dob')->nullable();
            $table->string('ethnicity')->nullable();
            $table->json('global_log')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
