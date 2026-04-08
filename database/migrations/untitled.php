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
        Schema::create('prize_draws', function (Blueprint $table) {
            $table->id();;
            $table->enum('draw_type', ['users', 'organisations'])->default('users');
            $table->date('draw_date');
            $table->integer('participants')->nullable();
            $table->integer('number_of_entries')->nullable();
            $table->integer('winner_id')->nullable(); // Changed from int to integer for Laravel migration syntax
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_draws');
    }
};
