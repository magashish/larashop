<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; 
use Illuminate\Support\Str; // Make sure to import the Str facade
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    
    protected $fillable = [
        'name',
        'description', 
        'icon',  
        'position'      
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }
  
    public function offers(): BelongsToMany
    {
        // The first argument is the related model (Offer::class)
        // The second argument is the pivot table name ('category_offer')
        // The third argument is the foreign key of the model on which you are defining the relationship (category_id)
        // The fourth argument is the foreign key of the model you are joining to (offer_id)
        return $this->belongsToMany(Offer::class, 'category_offer', 'category_id', 'offer_id');
    }


    /**
     * Get the organisations for the category through the offers.
     */
    public function organisations(): HasManyThrough
    {
        return $this->hasManyThrough(
            Organisation::class, // The final model you wish to access
            Offer::class,       // The intermediate model
            'category_id',      // Foreign key on the intermediate table ('category_offer')
            'id',               // Local key on the intermediate table ('offers')
            'id',               // Local key on the current model ('categories')
            'organisation_id'   // Foreign key on the final model's table that points to the intermediate model's table
        );
    }
}
