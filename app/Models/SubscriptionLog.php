<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionLog extends Model
{
    use HasFactory;

        /**
         * The attributes that are mass assignable.
         *
         * @var array<int, string>
         */
        protected $fillable = [
            'user_id',
            'subscription_id', // This could be the primary key of the 'subscriptions' table, or Stripe's subscription ID
            'event_type',      // e.g., 'created', 'swapped', 'cancelled', 'resumed', 'refunded'
            'details',         // JSON column to store additional relevant information
            'performed_by_user_id', // If an admin performs an action
        ];

        /**
         * The attributes that should be cast.
         *
         * @var array<string, string>
         */
        protected $casts = [
            'details' => 'array', // Cast the 'details' column to an array
        ];

        /**
         * Get the user that owns the subscription log.
         */
        public function user()
        {
            return $this->belongsTo(User::class);
        }

        // If 'subscription_id' refers to the ID in your local 'subscriptions' table (not Stripe's ID)
        // public function subscription()
        // {
        //     return $this->belongsTo(Subscription::class); // Assuming you have a Subscription model
        // }
    }
    