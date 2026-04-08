<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 5, 4)->comment('e.g. 0.1000 for 10%');
            $table->string('country')->default('AU');
            $table->string('state')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('priority')->default(0);
            $table->boolean('compound')->default(false)->comment('Applied on top of other taxes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_tax_rates');
    }
};
