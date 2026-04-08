<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'event',
        'message',
        'meta',
        'type',
        'typeid',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
