<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopOrder extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'status', 'payment_status', 'payment_method', 'payment_reference',
        'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone',
        'billing_address', 'billing_city', 'billing_state', 'billing_postcode', 'billing_country',
        'ship_to_billing', 'shipping_first_name', 'shipping_last_name',
        'shipping_address', 'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country',
        'subtotal', 'shipping_cost', 'tax_amount', 'discount_amount', 'total',
        'shop_coupon_id', 'shop_shipping_rate_id', 'notes', 'tracking_number', 'shipped_at',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'shipping_cost'   => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total'           => 'decimal:2',
        'ship_to_billing' => 'boolean',
        'shipped_at'      => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ShopOrderItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(ShopCoupon::class, 'shop_coupon_id');
    }

    public function shippingRate(): BelongsTo
    {
        return $this->belongsTo(ShopShippingRate::class, 'shop_shipping_rate_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(uniqid());
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'warning',
            'processing' => 'info',
            'shipped'    => 'primary',
            'delivered'  => 'success',
            'cancelled'  => 'danger',
            'refunded'   => 'secondary',
            default      => 'light',
        };
    }
}
