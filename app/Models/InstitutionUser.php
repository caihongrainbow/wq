<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\DataAccessHelper;
use App\Models\Role;
use App\Models\RoleLevel;
use Auth;

class InstitutionUser extends Authenticatable implements JWTSubject
{

    use HasRoles;
	use SoftDeletes;
    use Notifiable;
    use DataAccessHelper;

	protected $table = 'institution_users';

	protected $dates = ['deleted_at'];

    protected $guard_name = 'client';

	protected $fillable = ['user_id', 'ins_id', 'auth_id', 'is_admin', 'clientid'];

    /* 用于JSON WEB TOKEN 返回主键 */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /* 关联方法集合 Start */
    public function institution(){
    	return $this->belongsTo(Institution::class, 'ins_id', 'id');
    }

    public function user(){
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function credit()
    {
        return $this->hasOne(InstitutionUserCredit::class, 'custom_id', 'id');
    }

    /* 关联方法集合 End */

    /**
     * [getStoredRole 重写HasRoles中的方法]
     * @Author   CaiHong
     * @DateTime 2018-06-19
     * @param    [type]     $role [description]
     * @return   [type]           [description]
     */
    public function getStoredRole($role) : Role
    {
        if (is_numeric($role)) {
            return Role::where('id', $role)->where(function($query){
                $query->whereNull('ins_id')->orWhere('ins_id', $this->ins_id);
            })->where('guard_name', $this->getDefaultGuardName())->first();
        }

        if (is_string($role)) {
            return Role::where('name', $role)->where(function($query){
                $query->whereNull('ins_id')->orWhere('ins_id', $this->ins_id);
            })->where('guard_name', $this->getDefaultGuardName())->first();
        }

        return $role;
    }

    public function can($ability, $arguments = []){
        if($this->hasRole('admin')){
            return true;
        }

        $allPermissions = $this->getAllPermissions();
        
        $permission = collect($allPermissions)->filter(function($item, $key) use ($ability, $arguments) {
            return $item->name == $ability;
        });            
        if(!empty($arguments)){
            $permission = collect($permission)->filter(function($item, $key) use ($arguments){
                if(is_null($item['pivot']['entity'])){
                    return false;
                }
                $entity = unserialize($item['pivot']['entity']);
                if($arguments == Institution::class){
                    return in_array($this->ins_id, $entity[Institution::class]);
                }
                if($arguments instanceof Institution){
                    return in_array($this->ins_id, $entity[Institution::class]) || in_array($arguments->id, $entity[Institution::class]);
                }
                return false;
            });
        }
        return $permission->isNotEmpty();
    }

    public function assignRole(...$roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                return $this->getStoredRole($role);
            })
            ->each(function ($role) {
                $this->ensureModelSharesGuard($role);
            })
            ->all();

        $roleids = collect($roles)->pluck('id');

        $belongsTos = RoleLevel::whereIn('role_id', $roleids)->where('ins_id', $this->ins_id)->get();
        if($belongsTos){
            $belongsToids = $belongsTos->pluck('belongs_to')->unique();
            $filterids = RoleLevel::whereIn('belongs_to', $belongsToids)->get() ?? collect([]);
            $roleids = $filterids->pluck('role_id')->merge($roleids)->merge($belongsToids)->unique();
        }
        
        $this->roles()->detach($roleids);

        $belongsTos = $belongsTos->keyBy('role_id');

        $data = collect($roles)->map(function($item) use ($belongsTos) {
            return empty($belongsTos[$item->id]['belongs_to']) ? ['belongs_to' => null] : ['belongs_to' => $belongsTos[$item->id]['belongs_to']]; 
        })->all();

        $this->roles()->saveMany($roles, $data);

        $this->forgetCachedPermissions();

        return $this;
    }

}
