<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// IMPORTANT: Make sure these 'use' statements are present at the top
use App\Enums\OrganisationAddressType;
use App\Enums\OrganisationStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('admin_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('abn')->nullable();
            $table->string('website')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('twitter')->nullable();
            // Using App\Enums\OrganisationAddressType for enum values and default
            $table->enum('organisation_address_type', OrganisationAddressType::all())
                  ->default(OrganisationAddressType::ONLINE); // Make sure 'online' is the desired default
            $table->string('full_address')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('street_number', 24)->nullable();
            $table->string('street_name', 128)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('state', 3)->nullable();
            $table->string('postcode', 6)->nullable();
            $table->string('country', 24)->nullable();

            // Using App\Enums\OrganisationStatus for enum values and default
            $table->enum('processing_status', OrganisationStatus::all())
                  ->default(OrganisationStatus::NEW);
            $table->string('image')->nullable(); // Added 'images' column
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
