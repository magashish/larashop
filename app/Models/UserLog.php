<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'module',
        'module_action',
        'url'
    ];


    /**
     * Get the user that owns the user log.
     * This defines the inverse of the one-to-many relationship, allowing you to access the User model from a UserLog.
     */
    public function user()
    {
        return $this->belongsTo(User::class); // Assumes your User model is App\Models\User
    }
}