<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopTaxRate extends Model
{
    protected $fillable = [
        'name', 'rate', 'country', 'state', 'is_active', 'is_default', 'priority', 'compound',
    ];

    protected $casts = [
        'rate'       => 'decimal:4',
        'is_active'  => 'boolean',
        'is_default' => 'boolean',
        'compound'   => 'boolean',
    ];

    public function getRatePercentageAttribute(): float
    {
        return round($this->rate * 100, 2);
    }
}
