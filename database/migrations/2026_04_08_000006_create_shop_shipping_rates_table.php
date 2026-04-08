<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_shipping_zone_id');
            $table->string('name');
            $table->enum('type', ['flat', 'free', 'per_item', 'weight_based'])->default('flat');
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('min_order_amount', 10, 2)->nullable()->comment('For free shipping threshold');
            $table->decimal('max_weight', 8, 3)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('shop_shipping_zone_id')->references('id')->on('shop_shipping_zones')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_shipping_rates');
    }
};
