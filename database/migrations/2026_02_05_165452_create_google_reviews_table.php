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
        Schema::create('google_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('author_name');
            $table->string('author_img')->nullable(); 
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('review_text');
            $table->enum('type', ['google', 'facebook'])->default('google');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_reviews');
    }
};