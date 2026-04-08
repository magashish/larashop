<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Make sure to import the Str facade

class PrizeDrawEntryBackup extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prize_draw_entries_backups';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'package_subscriptions_id',
        'entries_added',
        'description',
        'type',
        'created_at', // Include timestamps here for transfer
        'updated_at',
    ];



     protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    /**
     * Define the relationship to the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship to the PrizeDraw model.
     */
    public function prizeDraw()
    {
        return $this->belongsTo(PrizeDraw::class);
    }
}
