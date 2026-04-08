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
        Schema::create('subscription_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            // You might store your local subscription ID or Stripe's ID
            $table->string('subscription_id')->nullable(); // Can be Stripe's sub ID or your local sub_id if applicable
            $table->string('event_type'); // e.g., 'created', 'swapped', 'cancelled', 'resumed', 'refunded'
            $table->json('details')->nullable(); // To store structured data about the event
            $table->foreignUuid('performed_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Optional: if an admin performs the action
            $table->timestamps();

            // Add an index for faster lookups
            $table->index(['user_id', 'event_type']);
            $table->index('subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_logs');
    }
};
