<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\AdminResetPasswordNotification; // Add this use statement at the top
use Illuminate\Support\Str; // Make sure to import the Str facade


// If you are using Spatie Permissions for admins as well, add this:
// use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // If you are using Spatie Permissions for admins as well, add this:
    // use HasRoles;

    // Set the table name to 'admins'
    protected $table = 'admins';

   // protected $table = 'admins';
    protected $guard_name = 'admin';

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
        'name',
        'email',
        'password',
        'two_factor_code',
        'two_factor_expires_at',
        'level',   
        'status', 
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
    ];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

     /**
     * Get the latest log entry for the admin.
     */
    public function latestLog()
    {
        return $this->hasOne(AdminLog::class, 'admin_id')->latestOfMany('created_at');
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


    public function sendPasswordResetNotification($token)
    {
        $notificationClass = config('auth.passwords.admin.email'); // Get the notification class from config
        // This is the actual dispatch. Replace the dd() with this line after testing.
        $this->notify(new $notificationClass($token));
    }
    
}
