<?php

namespace App\Models;

// If you're using Spatie's package, this is how your Role model should look:
use Spatie\Permission\Models\Role as SpatieRole; // Alias to avoid conflict if you have another 'Role'

class Role extends SpatieRole // Extend Spatie's Role model
{
    // If you need custom methods or relationships for your roles, add them here.
    // Spatie handles the basic table, primary key, fillable, etc.
    // You might still want to add HasFactory if you use factories for roles.
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    // If you need to explicitly define table/pk, though Spatie usually handles defaults:
    // protected $table = 'roles';
    // protected $primaryKey = 'id';
    // public $incrementing = true;
    // protected $keyType = 'int';

    // You might add fillable if you have custom columns, but Spatie's model already handles 'name', 'guard_name'
    // protected $fillable = ['name', 'guard_name'];

    // Example: If you want to relate roles to OrganisationUser (inverse relationship)
    public function organisationUsers()
    {
        return $this->belongsToMany(
            OrganisationUser::class,
            'user_organisation_roles',
            'role_id',
            'organisation_user_id'
        )->using(UserOrganisationRole::class)
         ->withTimestamps();
    }
}