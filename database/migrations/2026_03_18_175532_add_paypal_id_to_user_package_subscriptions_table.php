<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate
     * Adds paypal_id column to user_package_subscriptions table
     * stripe_id = Stripe payments
     * paypal_id = PayPal payments (capture ID or subscription ID)
     */
    public function up(): void
    {
        Schema::table('user_package_subscriptions', function (Blueprint $table) {
            $table->string('paypal_id')->nullable()->after('stripe_id')
                  ->comment('PayPal Capture ID (package) or Subscription ID (I-XXXXXXXX)');
        });
    }

    public function down(): void
    {
        Schema::table('user_package_subscriptions', function (Blueprint $table) {
            $table->dropColumn('paypal_id');
        });
    }
};