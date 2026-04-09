<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Color-specific image galleries
        Schema::create('shop_product_color_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_product_id');
            $table->string('color_name');           // e.g. "Black Stone"
            $table->string('image_path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('shop_product_id')->references('id')->on('shop_products')->onDelete('cascade');
            $table->index(['shop_product_id', 'color_name']);
        });

        // Per-variant stock: every combination of color + size
        Schema::create('shop_product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_product_id');
            $table->string('color_name');           // e.g. "Black Stone"
            $table->string('color_hex')->nullable();// e.g. "#2c2c2c"
            $table->string('color_swatch_image')->nullable(); // fabric texture image
            $table->string('size');                 // e.g. "XS","S","M","L","XL","2XL"
            $table->integer('size_order')->default(0); // for sorting sizes correctly
            $table->string('sku')->nullable()->unique();
            $table->integer('stock_quantity')->default(0);
            $table->decimal('price_adjustment', 8, 2)->default(0); // +/- on top of base price
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('shop_product_id')->references('id')->on('shop_products')->onDelete('cascade');
            $table->index(['shop_product_id', 'color_name', 'size']);
        });

        // Add variant_id to cart items
        Schema::table('shop_cart_items', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_product_variant_id')->nullable()->after('shop_product_id');
            $table->string('variant_color')->nullable()->after('shop_product_variant_id');
            $table->string('variant_size')->nullable()->after('variant_color');

            $table->foreign('shop_product_variant_id')->references('id')->on('shop_product_variants')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('shop_cart_items', function (Blueprint $table) {
            $table->dropForeign(['shop_product_variant_id']);
            $table->dropColumn(['shop_product_variant_id', 'variant_color', 'variant_size']);
        });
        Schema::dropIfExists('shop_product_variants');
        Schema::dropIfExists('shop_product_color_images');
    }
};
