<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->string('user_id')->nullable()->index();
            $table->unsignedBigInteger('shop_product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();

            $table->foreign('shop_product_id')->references('id')->on('shop_products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_cart_items');
    }
};
