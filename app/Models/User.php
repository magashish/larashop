<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Uncomment if you implement email verification
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;       // Required for one-to-many relationships
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Required for many-to-many relationships
// use Spatie\Permission\Traits\HasRoles; // Uncomment this line if you are using Spatie's Laravel-Permission package
use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Subscription;
use App\Notifications\UserResetPasswordNotification; // <-- Import your custom notification
use Illuminate\Support\Str; // Make sure to import the Str facade





class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;
    // use HasRoles; // Uncomment this line if you are using Spatie's Laravel-Permission package


     protected $keyType = 'string';
     public $incrementing = false; // <-- ADD THIS LINE


    protected $dates = [
        'two_factor_expires_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
        'google_id',
        'facebook_id',
        'sms_unsubscribed',
        'sms_unsubscribed_at',
        'email_unsubscribed',
        'email_unsubscribed_at',
        'level',
        'status',
        'exclude_from_prize_draw', 
        'two_factor_code',
        'two_factor_expires_at',
        'otp',
        'otp_expires_at',
        'stripe_id',
        'terms_accepted',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'exclude_from_prize_draw' => 'boolean',
        'sms_unsubscribed'       => 'boolean',
        'email_unsubscribed'     => 'boolean',
        'sms_unsubscribed_at'    => 'datetime',
        'email_unsubscribed_at'  => 'datetime',

    ];



     protected static function booted()
    {
        static::created(function ($user) {
            logUserActivity(
                $user->id,
                'user_created',
                'New user registered: ' . $user->name,
            [
                'username' => $user->name,
                'email' => $user->email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
            );
        });
    }



     protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }


      /**
     * Mark user as unsubscribed from SMS
     */
    public function unsubscribeSms(): void
    {
        $this->sms_unsubscribed    = true;
        $this->sms_unsubscribed_at = now();
        $this->save();
    }

    /**
     * Check if user is subscribed to SMS
     */
    public function isSmsSubscribed(): bool
    {
        return !$this->sms_unsubscribed;
    }

    /**
     * Mark user as unsubscribed from Email
     */
    public function unsubscribeEmail(): void
    {
        $this->email_unsubscribed    = true;
        $this->email_unsubscribed_at = now();
        $this->save();
    }

    /**
     * Check if user is subscribed to Email
     */
    public function isEmailSubscribed(): bool
    {
        return !$this->email_unsubscribed;
    }



      /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // Tell Laravel to use your custom notification for password resets
        $this->notify(new UserResetPasswordNotification($token));
    }

    

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the organisations created by this user.
     * An Organisation has a 'user_id' foreign key.
     */
    public function organisationsCreated(): HasMany
    {
        return $this->hasMany(Organisation::class, 'user_assign_id');
    }

    /**
     * Get the offers created by this user.
     * An Offer has a 'user_id' foreign key.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'user_assign_id');
    }

    /**
     * Get the organisations this user is associated with (many-to-many).
     * This uses the 'organisation_user' pivot table.
     */
    public function associatedOrganisations(): BelongsToMany
    {
        // 'organisation_user' is the pivot table name.
        // 'user_id' is the foreign key of this (User) model in the pivot table.
        // 'organisation_id' is the foreign key of the related (Organisation) model in the pivot table.
        // IMPORTANT: Ensure 'id' is included here to retrieve the pivot table's primary key
        return $this->belongsToMany(Organisation::class, 'organisation_user', 'user_assign_id', 'organisation_assign_id')
                  ->using(OrganisationUser::class)
                    // Include these extra columns from the pivot table when retrieving relationships.
                    ->withPivot('id', 'role', 'start_date', 'end_date','status') // <-- 'id' must be here
                    // Automatically manage created_at and updated_at on the pivot table.
                    ->withTimestamps();
    }



     // Prize Draw Entries
    public function mainEntries(): HasMany
    {
        return $this->hasMany(PrizeDrawEntry::class, 'user_id');
    }

    public function backupEntries(): HasMany
    {
        return $this->hasMany(PrizeDrawEntryBackup::class, 'user_id');
    }



// In app/Models/User.php
public function prizeDrawEntries()
{
    return $this->hasMany(PrizeDrawEntryLog::class); 
}

    /**
     * Get the Laravel Cashier subscriptions for the user.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the custom package subscriptions for the user.
     */
    public function userPackageSubscriptions(): HasMany
    {
        return $this->hasMany(UserPackageSubscription::class);
    }



     /**
     * Generate 6 digits MFA code for the User
     */
    public function generateTwoFactorCode()
    {
        $this->timestamps = false; //Dont update the 'updated_at' field yet
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    /**
     * Reset the MFA code generated earlier
     */
    public function resetTwoFactorCode()
    {
        $this->timestamps = false; //Dont update the 'updated_at' field yet
        
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }



    /**
     * Check if the user has a 'business' level.
     * Assumes you have a 'level' column with enum('business', 'member').
     */
    public function isBusinessAccount(): bool
    {
        return $this->level === 'business';
    }

    public function isSubBusinessAccount(): bool
    {
        return $this->level === 'sub_business';
    }

    /**
     * Check if the user has a 'member' level.
     */
    public function isMemberAccount(): bool
    {
        return $this->level === 'member';
    }



}
