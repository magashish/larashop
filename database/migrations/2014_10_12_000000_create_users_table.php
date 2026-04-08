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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name');
            $table->string('first_name')->nullable(); 
            $table->string('last_name')->nullable(); 
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mobile')->nullable(); 
            $table->string('password');
            $table->enum('level', ['business', 'member'])->default('member');
            $table->enum('status', ['active', 'deactive'])->default('active');
            $table->json('access_permissions')->nullable();
            $table->boolean('exclude_from_prize_draw')->default(false);
            $table->rememberToken();
            $table->string('profile_image')->nullable();
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();
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
        Schema::dropIfExists('users');
    }
};


