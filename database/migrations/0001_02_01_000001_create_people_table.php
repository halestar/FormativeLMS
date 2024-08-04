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
            $table->date('dob')->nullable();

            $table->bigInteger('ethnicity_id')->unsigned()->nullable();
            $table->foreign('ethnicity_id')->references('id')->on('crud_ethnicities')->onDelete('set null');
            $table->bigInteger('title_id')->unsigned()->nullable();
            $table->foreign('title_id')->references('id')->on('crud_titles')->onDelete('set null');
            $table->bigInteger('suffix_id')->unsigned()->nullable();
            $table->foreign('suffix_id')->references('id')->on('crud_suffixes')->onDelete('set null');
            $table->bigInteger('honors_id')->unsigned()->nullable();
            $table->foreign('honors_id')->references('id')->on('crud_honors')->onDelete('set null');
            $table->bigInteger('gender_id')->unsigned()->nullable();
            $table->foreign('gender_id')->references('id')->on('crud_gender')->onDelete('set null');
            $table->bigInteger('pronoun_id')->unsigned()->nullable();
            $table->foreign('pronoun_id')->references('id')->on('crud_pronouns')->onDelete('set null');

            $table->string('occupation')->nullable();
            $table->string('job_title')->nullable();
            $table->string('work_company')->nullable();
            $table->string('salutation')->nullable();
            $table->string('family_salutation')->nullable();
            $table->string('portrait_url')->nullable();
            $table->string('thumbnail_url')->nullable();

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
