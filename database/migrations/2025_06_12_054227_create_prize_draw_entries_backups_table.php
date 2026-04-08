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
        Schema::create('prize_draw_entries_backups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignUuid('package_subscriptions_id')->nullable()->constrained('user_package_subscriptions')->onDelete('cascade');
            $table->integer('entries_added')->default(1);
            $table->string('description')->nullable();
            $table->enum('type', ['paid', 'free','manual'])->default('paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_draw_entries_backups');
    }
};
