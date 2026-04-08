<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOrganisationRole extends Pivot
{
    protected $table = 'user_organisation_roles';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'organisation_user_id',
        'role_id',
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
    | Relationships for UserOrganisationRole (optional, for direct access)
    |--------------------------------------------------------------------------
    */

    /**
     * Get the OrganisationUser record this role assignment belongs to.
     */
    public function organisationUser(): BelongsTo
    {
        return $this->belongsTo(OrganisationUser::class, 'organisation_user_id');
    }

    /**
     * Get the Role record this assignment belongs to.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}