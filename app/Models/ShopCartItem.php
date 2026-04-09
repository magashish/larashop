<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopCartItem extends Model
{
    protected $fillable = [
        'session_id', 'user_id', 'shop_product_id', 'shop_product_variant_id',
        'variant_color', 'variant_size', 'quantity', 'unit_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'shop_product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ShopProductVariant::class, 'shop_product_variant_id');
    }

    public function getSubtotalAttribute(): float
    {
        return round($this->unit_price * $this->quantity, 2);
    }
}
