<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPackage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'entries_into_draw',
        'access_duration_weeks',
        'description',
    ];

    /**
     * Get the user packages that belong to the package.
     */
    public function userPackages()
    {
        return $this->hasMany(UserPackage::class);
    }
}
