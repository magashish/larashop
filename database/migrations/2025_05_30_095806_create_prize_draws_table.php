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
        // Schema::create('prize_draws', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        //     $table->foreignUuid('admin_id')->nullable()->constrained()->onDelete('set null');
        //     $table->string('title'); 
        //     $table->decimal('money', 10, 2);
        //     $table->enum('draw_type', ['subscribers', 'packages_subscribers'])->default('subscribers');
        //     $table->timestamp('draw_date');
        //     $table->integer('participants')->nullable();
        //     $table->foreignUuid('winner_id')->nullable()->constrained('users')->onDelete('set null');
        //     $table->timestamp('draw_datetime')->nullable();
        //     $table->enum('winner_user_finalised', ['yes', 'no'])->default('no');
        //      // --- NEW REMINDER COLUMNS ---
        //     $table->enum('weekly_draw_reminder', ['yes', 'no'])->default('no');
        //     $table->enum('retarget_past_customers', ['yes', 'no'])->default('no');
        //     // --- END NEW REMINDER COLUMNS ---
        //     $table->softDeletes();
        //     $table->timestamps();
        // });

        Schema::create('prize_draws', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('admin_id')
            ->nullable()
            ->constrained()
            ->onDelete('set null');

            // Admin / internal name
            $table->string('title');

            // Prize logic
            $table->enum('prize_type', ['cash', 'non_cash']);
            $table->decimal('cash_amount', 15, 2)->nullable();
            $table->string('prize_title')->nullable();
            $table->string('prize_sub_title')->nullable();
            $table->decimal('prize_value_amount', 15, 2)->nullable(); // value of non-cash prize
            $table->text('prize_description');
            $table->string('prize_image');

            // Draw logic
            $table->enum('draw_type', ['subscribers', 'packages'])->default('subscribers');
            $table->timestamp('draw_date')->nullable(false)->change();

           // Winner logic
            $table->integer('participants')->nullable();
            $table->foreignUuid('winner_id')
            ->nullable()
            ->constrained('users')
            ->onDelete('set null');

            $table->timestamp('draw_datetime')->nullable();
            $table->enum('winner_user_finalised', ['yes', 'no'])->default('no');

           // Marketing flags
            $table->enum('weekly_draw_reminder', ['yes', 'no'])->default('no');
            $table->enum('retarget_past_customers', ['yes', 'no'])->default('no');
            $table->boolean('show_on_site')->default(false);



            $table->softDeletes();
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
