<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

	protected $guard_name = 'web'; 

	public function institutionUsers(){
		return $this->belongsToMany(InstitutionUser::class, 'model_has_roles', 'role_id', 'model_id')->wherePivot('model_type', 'App\Models\InstitutionUser');
	}

	public function levels(){
		return $this->hasMany(RoleLevel::class, 'belongs_to', 'id');
	}

	public function contact(){
		return $this->hasOne(RoleLevel::class, 'role_id', 'id');
	}

	public function mytest(){
		return false;
	}

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions')
        )->withPivot('entity');
    }

    public function givePermissionTo(...$permissions)
    {
    	$perms = $permissions[0];
    	$entities = $permissions[1];

        $permissions = collect($perms)
            ->flatten()
            ->map(function ($permission) {
                return $this->getStoredPermission($permission);
            })
            ->each(function ($permission) {
                $this->ensureModelSharesGuard($permission);
            })
            ->all();

        $entities = collect($entities)->map(function($item){
            return is_array($item) ? collect($item)->keyBy(function($value, $key){
                if($key == 'shop'){
                    return Institution::class;
                }
                return $key;
            })->all() : $item;
        })->map(function($item){
            return is_array($item) ? ['entity' => serialize($item)] : ['entity' => null];
        })->all();
   
        $this->permissions()->saveMany($permissions, $entities);

        $this->forgetCachedPermissions();

        return $this;
    }
}
