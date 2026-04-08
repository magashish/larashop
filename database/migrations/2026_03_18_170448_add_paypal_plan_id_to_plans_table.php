<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate
     * Yeh column subscription plans ke liye PayPal Billing Plan ID store karta hai
     * One-time package ke liye zaroori nahi — sirf recurring subscription ke liye
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('paypal_plan_id')->nullable()->after('stripe_plan')
                  ->comment('PayPal Billing Plan ID e.g. P-XXXXXXXXXX — sirf subscription ke liye');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('paypal_plan_id');
        });
    }
};