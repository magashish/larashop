<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // <--- Add this
use Illuminate\Database\Eloquent\Relations\BelongsTo;    // <--- Add this

class OrganisationUser extends Pivot
{
    protected $table = 'organisation_user';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', // <--- Make sure 'id' is in fillable if you're ever explicitly setting it or using create() on it with a pre-set ID
        'user_assign_id',
        'organisation_assign_id',
        'admin_id',
        'role',
        'start_date',
        'end_date',
        'status',
        'user_id', // <--- Add 'user_id' if it's in your migration and you're filling it
    ];

    protected $casts = [ // <--- Add casts if not already there
        'role' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships for OrganisationUser
    |--------------------------------------------------------------------------
    */

    // This relationship links back to the main User model (who is assigned)
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_assign_id');
    }

    // This relationship links back to the Organisation model
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisation_assign_id');
    }

    // This relationship links to the User who created the association (if user_id is the creator)
    public function creatorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id'); // Assuming user_id column
    }

    /**
     * Get the Roles associated with this specific organisation-user record.
     * THIS IS THE CRITICAL METHOD FOR YOUR roles()->sync() CALLS.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,             // The related model
            'user_organisation_roles', // The intermediate pivot table's name
            'organisation_user_id',    // Foreign key on 'user_organisation_roles' that points to THIS model (OrganisationUser)'s ID
            'role_id'                  // Foreign key on 'user_organisation_roles' that points to the 'Role' model's ID
        )->using(UserOrganisationRole::class) // Specify the intermediate pivot model
         ->withTimestamps(); // If user_organisation_roles table has created_at/updated_at
    }
}