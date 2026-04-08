<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'quantity',
        'price',
        'license_type',
        'is_active',
        'is_public',
    ];
}
