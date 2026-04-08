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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admin_id')->nullable()->constrained()->onDelete('set null');
            $table->string('influencer')->nullable(); // Added influencer field
            $table->string('promo_code')->unique(); // Changed 'code' to 'promo_code' as per your form
            $table->date('start_date'); // Added start_date
            $table->date('end_date');   // Added end_date
            // Removed: type, value, max_uses, uses, expires_at, is_active, description
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
