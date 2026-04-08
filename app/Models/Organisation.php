<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Illuminate\Support\Str; // Make sure to import the Str facade

class Organisation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $keyType = 'string';
    public $incrementing = false; // <-- ADD THIS LINE

    protected $fillable = [
        'title',
        'abn',
        'website',
        'instagram',
        'facebook',
        'youtube',
        'tiktok',
        'twitter',
        'organisation_address_type',
        'full_address',
        'latitude',
        'longitude',
        'street_number',
        'street_name',
        'city',
        'state',
        'postcode',
        'country',
        'processing_status',
        'image',
        'merchantid',
        'parent_id',
        'user_id', // <-- This remains for the 'creator' (one-to-many)
        'admin_id', // <-- This remains for the 'creator' (one-to-many)
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'organisation_address_type' => 'string',
        'full_address' => 'string', // Ensure full_address is cast as string
        'processing_status' => 'string',
        'image' => 'string',
    ];




     protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    /**
     * Get the user that created the organisation (one-to-many).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin that created the organisation (one-to-many).
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the users associated with this organisation (many-to-many).
     * This represents users who are members or have access to this organisation.
     * Includes pivot data for role, start_date, and end_date.
     */
    public function PendingassociatedUsers(): BelongsToMany
    {

        // The first argument is the related model (User::class)
        // The second argument is the pivot table name ('organisation_user')
        // The third argument is the foreign key of the model on which you are defining the relationship (organisation_id)
        // The fourth argument is the foreign key of the model you are joining to (user_id)
        return $this->belongsToMany(User::class, 'organisation_user', 'organisation_assign_id', 'user_assign_id')
                    ->withPivot('id', 'role', 'start_date', 'end_date') // <-- Include pivot 'id' here
                    ->wherePivot('status', 'pending') // <--- Add this line to filter by status on the pivot table
                    ->withTimestamps();
                }

                public function ApproveassociatedUsers(): BelongsToMany
                {

        // The first argument is the related model (User::class)
        // The second argument is the pivot table name ('organisation_user')
        // The third argument is the foreign key of the model on which you are defining the relationship (organisation_id)
        // The fourth argument is the foreign key of the model you are joining to (user_id)
                    return $this->belongsToMany(User::class, 'organisation_user', 'organisation_assign_id', 'user_assign_id')
                    ->withPivot('id', 'role', 'start_date', 'end_date') // <-- Include pivot 'id' here
                    ->wherePivot('status','approve')
                    ->withTimestamps();
                }

                public function AllassociatedUsers(): BelongsToMany
                {
        // The first argument is the related model (User::class)
        // The second argument is the pivot table name ('organisation_user')
        // The third argument is the foreign key of the model on which you are defining the relationship (organisation_id)
        // The fourth argument is the foreign key of the model you are joining to (user_id)
                    return $this->belongsToMany(User::class, 'organisation_user', 'organisation_assign_id', 'user_assign_id')
                    ->using(OrganisationUser::class)
                    ->withPivot('id', 'role', 'start_date', 'end_date') // <-- Include pivot 'id' here
                    ->withTimestamps();
                }


                public function activeAllassociatedUsers(): BelongsToMany
                {
                    $currentDate = Carbon::now();

                    return $this->belongsToMany(User::class, 'organisation_user', 'organisation_assign_id', 'user_assign_id')
                    ->withPivot('id', 'role', 'start_date', 'end_date', 'status')
                    ->wherePivot('start_date', '<=', $currentDate)
                    ->wherePivot('status', 'approve')
                    ->where(function ($query) use ($currentDate) {
                    $query->where('organisation_user.end_date', '>=', $currentDate) 
                          ->orWhereNull('organisation_user.end_date');
                      })
                    ->withTimestamps();
                }




                public function offers(): HasMany
                {
                    return $this->hasMany(Offer::class, 'organisation_id');
                }

    /**
     * Get offers with 'pending' processing status.
     */
    public function pendingOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'organisation_id')
        ->where('processing_status', 'new'); 
    }

    public function rejectedOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'organisation_id')
        ->where('processing_status', 'rejected'); 
    }

    /**
     * Get offers that have expired.
     * An offer is expired if its 'end_date' is in the past.
     */
    public function expiredOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'organisation_id')
                ->where('end_date', '<', Carbon::today()); // Condition 3
            }

    /**
     * Get offers that are currently active.
     * An offer is active if its 'processing_status' is 'approved',
     * its 'start_date' is in the past or present, AND its 'end_date' is in the future or null.
     */
    public function activeOffers(): HasMany
    {
        // return $this->hasMany(Offer::class, 'organisation_id')
        // ->where('processing_status', 'approved')
        //         ->where('start_date', '<=', Carbon::today()) 
        //         ->where(function ($query) {
        //             $query->whereNull('end_date')
        //                   ->orWhere('end_date', '>=', Carbon::today()); 
        //               });

        return $this->hasMany(Offer::class, 'organisation_id')
        ->where('processing_status', 'approved');
    }

    /**
     * Get all offers for the organisation, regardless of status or expiry.
     * (This is likely what your original 'offers' relationship already does).
     */
    public function allOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'organisation_id');
    }
}
