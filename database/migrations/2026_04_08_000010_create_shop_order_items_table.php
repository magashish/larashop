<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_order_id');
            $table->unsignedBigInteger('shop_product_id')->nullable();
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('shop_order_id')->references('id')->on('shop_orders')->onDelete('cascade');
            $table->foreign('shop_product_id')->references('id')->on('shop_products')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_order_items');
    }
};
