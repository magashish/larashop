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
        Schema::create('user_package_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['subscription','package']);
            $table->timestamp('purchase_date')->useCurrent();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->enum('status', ['active','suspended','canceled','future','expired','incomplete','incomplete_expired','trialing','past_due','unpaid']);
            $table->string('stripe_id')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_upsell')->default(false);

            // Add new columns here
           $table->string('stripe_customer_id', 128)->nullable();
           $table->decimal('price_excl_gst', 10, 2)->default(0.00);
           $table->decimal('price_gst', 10, 2)->default(0.00);
           $table->decimal('price_incl_gst', 10, 2)->default(0.00);
           $table->enum('package_expiry_reminder', ['yes', 'no'])->default('no');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_package_subscriptions');
    }
};

