<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shop_category_id', 'name', 'slug', 'short_description', 'description',
        'price', 'sale_price', 'cost_price', 'sku', 'stock_quantity', 'track_stock',
        'allow_backorders', 'weight', 'featured_image', 'is_active', 'is_featured', 'meta',
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'sale_price'      => 'decimal:2',
        'cost_price'      => 'decimal:2',
        'weight'          => 'decimal:3',
        'track_stock'     => 'boolean',
        'allow_backorders'=> 'boolean',
        'is_active'       => 'boolean',
        'is_featured'     => 'boolean',
        'meta'            => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ShopCategory::class, 'shop_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ShopProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ShopProductVariant::class)->orderBy('color_name')->orderBy('size_order');
    }

    public function colorImages(): HasMany
    {
        return $this->hasMany(ShopProductColorImage::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class);
    }

    /** Unique colors available for this product */
    public function getColorsAttribute(): \Illuminate\Support\Collection
    {
        return $this->variants
            ->groupBy('color_name')
            ->map(fn($v) => $v->first())
            ->values();
    }

    /** All sizes available across all colors */
    public function getSizesAttribute(): \Illuminate\Support\Collection
    {
        return $this->variants
            ->sortBy('size_order')
            ->unique('size')
            ->values();
    }

    public function isInStock(): bool
    {
        if ($this->variants->isNotEmpty()) {
            return $this->variants->where('is_active', true)->sum('stock_quantity') > 0;
        }
        if (!$this->track_stock) {
            return true;
        }
        return $this->stock_quantity > 0 || $this->allow_backorders;
    }

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
