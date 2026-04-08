<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

    // USER
            $table->uuid('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('stripe_customer_id')->nullable()->index();

    // STRIPE IDS
            $table->string('stripe_object_id')->unique();
    $table->string('object_type')->index();                        // 'invoice' | 'payment_intent' | 'charge'
    $table->string('stripe_payment_intent_id')->nullable()->index();
    $table->string('stripe_invoice_id')->nullable()->index();
    $table->string('stripe_charge_id')->nullable()->index();
    $table->string('stripe_subscription_id')->nullable()->index();

    // PRODUCT
    $table->string('type')->nullable()->index();                   // 'subscription' | 'one_time'
    $table->string('product_name')->nullable();
    $table->string('price_id')->nullable();
    $table->string('description')->nullable();
    $table->integer('quantity')->default(1);

    // MONEY
    $table->decimal('amount', 12, 2)->default(0);
    $table->decimal('amount_refunded', 12, 2)->default(0);
    $table->decimal('stripe_fee', 12, 2)->nullable();
    $table->decimal('net_amount', 12, 2)->nullable();
    $table->string('currency', 10)->default('aud');

    // STATUS
    $table->string('status')->nullable()->index();                 // 'paid' | 'pending' | 'failed' | 'refunded'
    $table->timestamp('paid_at')->nullable()->index();
    $table->timestamp('refunded_at')->nullable();

    // BILLING ADDRESS
    $table->string('billing_name')->nullable();
    $table->string('billing_email')->nullable();
    $table->string('billing_city')->nullable();
    $table->string('billing_state')->nullable()->index();
    $table->string('billing_country', 5)->nullable();
    $table->string('billing_postcode')->nullable();

    // TAX
    $table->decimal('tax_amount', 12, 2)->nullable();
    $table->string('tax_rate')->nullable();

    // EXTRA
    $table->string('payment_method_type')->nullable();
    $table->string('receipt_url')->nullable();
    $table->json('raw_data')->nullable();
    $table->json('metadata')->nullable();

    $table->timestamps();
    $table->softDeletes();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};