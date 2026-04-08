<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Make sure to import the Str facade

use App\Models\UserPackageSubscription; // Import this model


class PrizeDrawEntry extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prize_draw_entries';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'package_subscriptions_id',
        'prize_draw_id',
        'entries_added',
        'description',
        'type',
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
     * An entry log belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function prizeDraw()
    {
        return $this->belongsTo(PrizeDraw::class);
    }

    public function packageSubscription()
    {
        return $this->belongsTo(UserPackageSubscription::class, 'package_subscriptions_id');
    }


}
