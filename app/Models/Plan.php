<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'stripe_plan', 
        'paypal_plan_id', 
        'entries_into_draw',
        'access_duration_weeks',
        'price',
        'price_description',
        'is_public',
        'description',
        'small_description',
        'read_more',
        'sub_description',
        'payment_description',
        'image',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float', 
        'is_public' => 'boolean',
    ];

    // Optional: Add any relationships or helper methods here

    /**

     * Write code on Method

     *

     * @return response()

     */

    // public function getRouteKeyName()

    // {
    //     return 'slug';
    // }

     /**
     * Get the user package subscriptions that belong to this plan.
     */
    public function userPackageSubscriptions(): HasMany
    {
        return $this->hasMany(UserPackageSubscription::class);
    }
}
