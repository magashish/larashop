<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Make sure to import the Str facade

class Events extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $keyType = 'string';


    protected $fillable = [
        'admin_id',
        'name',
        'description',
        'start',
        'end',
        'all_day',
        'location',
        'event_category',
        'image',
        'facebook_url',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'all_day' => 'boolean',
    ];


     protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    /**
     * Get the admin that owns the event.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}