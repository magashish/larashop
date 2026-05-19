<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // Seed default shipping & returns content
        DB::table('shop_settings')->insert([
            'key'        => 'shipping_returns',
            'value'      => "Orders are dispatched within 1–3 business days. Standard delivery 3–7 business days.\n\nFree shipping on orders over \$100.\n\nReturns accepted within 30 days of purchase. Items must be unworn and in original packaging.",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};
