<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstagramToken extends Model
{
    protected $fillable = ['access_token', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Expiry check
    public function isExpiringSoon(): bool
    {
        return $this->expires_at->diffInDays(now()) <= 10;
    }
}
