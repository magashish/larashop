<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopShippingZone extends Model
{
    protected $fillable = [
        'name', 'description', 'countries', 'states', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'countries' => 'array',
        'states'    => 'array',
        'is_active' => 'boolean',
    ];

    public function rates(): HasMany
    {
        return $this->hasMany(ShopShippingRate::class);
    }
}
