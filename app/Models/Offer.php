<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // IMPORTANT: Add this use statement
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany; // IMPORTANT: Include this for HasMany relationship
use Illuminate\Support\Str; // Make sure to import the Str facade

class Offer extends Model
{
    use HasFactory; // IMPORTANT: Add this trait

    protected $keyType = 'string';
   
    public $incrementing = false; // <<< --- IMPORTANT: CHANGE THIS TO FALSE ---


    protected $fillable = [
        'parent_id',
        'admin_id',
        'user_id',
        'title',
        'custom_redemption_code',
        'custom_redemption_description',
        'custom_redemption_url',
        'redemption_limit',
        'full_price',
        'discount_price',
        'percentage_off',
        'custom_saving',
        'description',
        'terms_conditions',
        'organisation_id',
        'offer_address_type',
        'full_address',
        'latitude',
        'longitude',
        'street_number',
        'street_name',
        'city',
        'state',
        'postcode',
        'country',
        'start_date',
        'no_end_date',
        'end_date',
        'processing_status',
        'is_featured',
        'is_paid_advertisement',
        'banner_image',
        'offer_url',
        'image_file', 
        'importofferid', 
        'offer_ongoing_subscription',
    ];

    protected $casts = [
        'start_date' => 'date',
        'no_end_date' => 'boolean',
        'end_date' => 'date',
        'full_price' => 'decimal:2', 
        'discount_price' => 'decimal:2', 
        'percentage_off' => 'decimal:2', 
        'processing_status' => 'string', 
        'image_file' => 'array',
    ];

      protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }


     public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

     public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

  
    /**
     * Define the many-to-many relationship with Category.
     * An Offer can belong to many Categories.
     */
    public function categories(): BelongsToMany
    {
    return $this->belongsToMany(Category::class, 'category_offer', 'offer_id', 'category_id')->withTimestamps();
    }

    /**
     * Define the one-to-many relationship with OfferAttachment.
     * An Offer can have many attachments.
     */
    public function attachments(): HasMany
    {
        // 'offer_id' is the foreign key on the 'offer_attachments' table
        // 'id' is the local key on the 'offers' table
        return $this->hasMany(OfferAttachment::class, 'offer_id', 'id');
    }
}
