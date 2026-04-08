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
        Schema::create('organisation_user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admin_id')->nullable()->constrained()->onDelete('set null'); 
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUuid('organisation_assign_id')->constrained('organisations')->onDelete('cascade');
            $table->foreignUuid('user_assign_id')->constrained('users')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('role')->nullable(); 
            $table->enum('status', ['pending','reject', 'approve'])->default('approve');
            $table->timestamps();

            // Enforce uniqueness for the combination of organisation, user.
            // but each specific association (defined by org, user) must be unique.
           // $table->unique(['organisation_id', 'user_id'], 'org_user_unique');

            // Enforce uniqueness for the combination of organisation, user, and start_date.
            // This allows a user to be associated with the same organisation multiple times,
            // but each specific association (defined by org, user, and start date) must be unique.
            //$table->unique(['organisation_id', 'user_id', 'start_date'], 'org_user_start_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation_user');
    }
};
