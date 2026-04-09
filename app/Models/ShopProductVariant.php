<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProductVariant extends Model
{
    protected $fillable = [
        'shop_product_id', 'color_name', 'color_hex', 'color_swatch_image',
        'size', 'size_order', 'sku', 'stock_quantity', 'price_adjustment', 'is_active',
    ];

    protected $casts = [
        'stock_quantity'   => 'integer',
        'price_adjustment' => 'decimal:2',
        'is_active'        => 'boolean',
        'size_order'       => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'shop_product_id');
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function getFinalPriceAttribute(): float
    {
        $base = $this->product->sale_price ?? $this->product->price;
        return round((float) $base + (float) $this->price_adjustment, 2);
    }
}
