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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('admin_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['subscription','package']);
            $table->string('stripe_plan')->unique()->nullable(); 
            $table->integer('entries_into_draw')->nullable(); 
            $table->integer('access_duration_weeks')->nullable();
            $table->decimal('price', 8, 2)->default(0.00)->nullable()->comment('Price of the plan or product.');
            $table->text('price_description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->text('description')->nullable();
            $table->text('small_description')->nullable();
            $table->text('read_more')->nullable();
            $table->text('sub_description')->nullable();
            $table->text('payment_description')->nullable();
            $table->string('image')->nullable(); 
            $table->integer('position')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

