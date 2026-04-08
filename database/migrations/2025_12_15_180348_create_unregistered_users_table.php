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
        Schema::create('unregistered_users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name')->nullable();
            $table->string('email')->index();
            $table->string('mobile')->nullable()->index();
            $table->string('source')->nullable();
            $table->boolean('is_registered')->default(false);
            // Follow-up system
            $table->unsignedTinyInteger('followup_step')->default(1);
            $table->timestamp('next_followup_at')->nullable();
            // Status
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('sms_sent_at')->nullable();

            $table->boolean('sms_unsubscribed')->default(false);
            $table->timestamp('sms_unsubscribed_at')->nullable();
            $table->boolean('email_unsubscribed')->default(false);
            $table->timestamp('email_unsubscribed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unregistered_users');
    }
};
