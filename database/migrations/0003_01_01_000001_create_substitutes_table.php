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
        Schema::create('substitutes', function (Blueprint $table) {
            $table->foreignId('person_id')->primary()->constrained('people')->cascadeOnDelete();
			$table->foreignId('phone_id')->nullable()->constrained('phones')->cascadeOnDelete();
            $table->boolean('sms_confirmed')->default(false);
            $table->boolean('email_confirmed')->default(false);
            $table->dateTime('account_verified')->nullable();
            $table->dateTime('sms_verified')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substitutes');
    }
};
