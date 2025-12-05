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
        Schema::create('school_messages', function (Blueprint $table) {
            $table->id();
            $table->boolean('system')->default(false);
            $table->boolean('subscribable')->default(true);
            $table->boolean('force_subscribe')->default(false);
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('send_email')->default(true);
            $table->boolean('send_sms')->default(false);
            $table->boolean('send_push')->default(false);
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('short_subject')->nullable();
            $table->text('short_body')->nullable();
            $table->string('notification_class');
            $table->string('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_messages');
    }
};
