<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable; // ← Add this
use Illuminate\Support\Str; // Make sure to import the Str facade



class UnregisteredUser extends Model
{
    use HasFactory, Notifiable;


     protected $keyType = 'string';
     public $incrementing = false; // <-- ADD THIS LINE

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'source',
        'is_registered',
        'followup_step',
        'next_followup_at',
        'email_sent_at',
        'sms_sent_at',
        'sms_unsubscribed',
        'sms_unsubscribed_at',
        'email_unsubscribed',
        'email_unsubscribed_at',
    ];

    protected $casts = [
        'next_followup_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'sms_sent_at' => 'datetime',
        'is_registered' => 'boolean',
        'sms_unsubscribed'       => 'boolean',
        'email_unsubscribed'     => 'boolean',
        'sms_unsubscribed_at'    => 'datetime',
        'email_unsubscribed_at'  => 'datetime',
    ];

       protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

     /*
    |--------------------------------------------------------------------------
    | Unsubscribe helpers
    |--------------------------------------------------------------------------
    */

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

    public function smsUnsubscribeLink(): string
    {
        $token = encrypt($this->id); 
        return route('unsubscribe.sms', ['token' => $token]);
    }

}
