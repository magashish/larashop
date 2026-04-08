<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopShippingRate extends Model
{
    protected $fillable = [
        'shop_shipping_zone_id', 'name', 'type', 'rate', 'min_order_amount', 'max_weight', 'is_active',
    ];

    protected $casts = [
        'rate'             => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_weight'       => 'decimal:3',
        'is_active'        => 'boolean',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShopShippingZone::class, 'shop_shipping_zone_id');
    }
}
