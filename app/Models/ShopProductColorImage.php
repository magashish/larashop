<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProductColorImage extends Model
{
    protected $fillable = [
        'shop_product_id', 'color_name', 'image_path', 'sort_order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'shop_product_id');
    }
}
