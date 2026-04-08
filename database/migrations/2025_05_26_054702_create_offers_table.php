<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\OfferAddressType;
use App\Enums\OrganisationStatus;
use App\Enums\YesNo;

class CreateOffersTable extends Migration
{
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('admin_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->enum('redemption_process', ['standard', 'custom'])->default('custom');
            $table->string('custom_redemption_code')->nullable();
            $table->text('custom_redemption_description')->nullable();
            $table->string('custom_redemption_url')->nullable();
            $table->integer('redemption_limit')->default(0);
            $table->enum('pricing', ['fixed', 'percentage', 'custom'])->default('fixed');
            $table->decimal('full_price', 10, 2)->default(0.00);
            $table->decimal('discount_price', 10, 2)->default(0.00);
            $table->decimal('percentage_off', 5, 2)->nullable();
            $table->string('custom_saving')->nullable();
            $table->text('description')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->foreignUuid('organisation_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('offer_address_type', OfferAddressType::all())
                  ->default(OfferAddressType::USE_BUSSINESS_ADDRESS);
            $table->string('full_address')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('street_number', 24)->nullable();
            $table->string('street_name', 128)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('state', 3)->nullable();
            $table->string('postcode', 6)->nullable();
            $table->string('country', 24)->nullable();
            $table->date('start_date')->nullable();
            $table->boolean('no_end_date')->default(false);
            $table->date('end_date')->nullable();
            $table->enum('processing_status', OrganisationStatus::all())->default(OrganisationStatus::NEW);
            $table->enum('is_featured', YesNo::all())->default(YesNo::NO);
            $table->enum('is_paid_advertisement', YesNo::all())->default(YesNo::NO);
            $table->string('banner_image')->nullable();
            $table->string('offer_url')->nullable();
            $table->json('image_file')->nullable(); // Changed from string to json
            $table->string('importofferid')->nullable();
            $table->enum('offer_ongoing_subscription', ['yes', 'no'])->default('no');
            $table->timestamps();
        });

        Schema::create('category_offer', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('category_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('offer_id')->constrained()->onDelete('cascade'); 
            $table->timestamps();
        });
        Schema::create('offer_attachments', function (Blueprint $table) {
            $table->id(); 
            $table->foreignUuid('offer_id')->constrained()->onDelete('cascade');
            $table->string('file_image');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_offer');
        Schema::dropIfExists('offers');
        Schema::dropIfExists('offer_attachments');
    }
}
