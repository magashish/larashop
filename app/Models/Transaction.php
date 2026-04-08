<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // USER
        'user_id',
        'stripe_customer_id',

        // STRIPE IDS
        'stripe_object_id',
        'object_type',
        'stripe_payment_intent_id',
        'stripe_invoice_id',
        'stripe_charge_id',
        'stripe_subscription_id',

        // PRODUCT
        'type',
        'product_name',
        'price_id',
        'description',
        'quantity',

        // MONEY
        'amount',
        'amount_refunded',
        'stripe_fee',
        'net_amount',
        'currency',

        // STATUS
        'status',
        'paid_at',
        'refunded_at',

        // BILLING ADDRESS
        'billing_name',
        'billing_email',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_postcode',

        // TAX
        'tax_amount',
        'tax_rate',

        // EXTRA
        'payment_method_type',
        'receipt_url',
        'raw_data',
        'metadata',
    ];

    protected $casts = [
        'paid_at'      => 'datetime',
        'refunded_at'  => 'datetime',
        'raw_data'     => 'array',
        'metadata'     => 'array',
        'amount'       => 'decimal:2',
        'amount_refunded' => 'decimal:2',
        'stripe_fee'   => 'decimal:2',
        'net_amount'   => 'decimal:2',
        'tax_amount'   => 'decimal:2',
    ];

    // RELATIONSHIPS
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}