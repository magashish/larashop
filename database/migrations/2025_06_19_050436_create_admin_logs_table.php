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
    
         // Create the 'admin_logs' table
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignUuid('admin_id')->constrained('admins') ->onDelete('cascade');
            $table->string('module', 30)->nullable(false); 
            $table->string('module_action', 30)->nullable(); 
            $table->string('url', 255)->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
