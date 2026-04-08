<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str; // Make sure to import the Str facade

class UserPackageSubscription extends Model
{
    use HasFactory;

     protected $keyType = 'string';
     public $incrementing = false; // <-- ADD THIS LINE

    // Define the table name if it's not the plural of the model name (which it is here, but good practice)
    protected $table = 'user_package_subscriptions';

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'plan_id',
        'type',
        'purchase_date',
        'starts_at',
        'ends_at',
        'status',
        'stripe_id',
        'paypal_id',
        'comment',
        'is_upsell', 
        'package_expiry_reminder',
    ];

    // Define attributes that should be cast to native types.
    // Timestamps should be cast to Carbon instances for easy manipulation.
    protected $casts = [
        'purchase_date' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_upsell'     => 'boolean',
    ];



     protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }
    

    // Define relationships

    /**
     * Get the user that owns the subscription/package.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with the subscription/package.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    // --- Scopes (Optional but Recommended) ---
    // Scopes allow you to encapsulate common query logic.

    /**
     * Scope a query to only include active subscriptions/packages.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where(function ($q) {
                         $q->whereNull('ends_at')
                           ->orWhere('ends_at', '>', now());
                     });
    }

    /**
     * Scope a query to only include subscriptions.
     */
    public function scopeSubscriptions($query)
    {
        return $query->where('type', 'subscription');
    }

    /**
     * Scope a query to only include packages.
     */
    public function scopePackages($query)
    {
        return $query->where('type', 'package');
    }

    // --- Accessors/Mutators (Optional) ---

    /**
     * Determine if the subscription/package is currently active.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' &&
               (is_null($this->ends_at) || $this->ends_at->isFuture());
    }
}