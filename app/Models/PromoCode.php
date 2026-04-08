<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Make sure to import the Str facade

class PromoCode extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promo_codes'; // Explicitly define table name

      protected $keyType = 'string';
     public $incrementing = false; // <-- ADD THIS LINE

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'influencer',
        'promo_code',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

      protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    // No relationships or custom methods are needed based on the provided migration fields.
    // If you add more fields or relationships later (e.g., to a User for the influencer),
    // you would add them here.
}
