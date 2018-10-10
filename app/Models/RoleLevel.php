<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleLevel extends Model
{
    protected $fillable = ['apex','level','role_id','ins_id'];

    public function getByLevel($insid, $role_id, $level){
    	$roleLevel = $this::where(['ins_id' => $insid, 'role_id' => $role_id, 'level' => $level])->first() ?? [];

    	return $roleLevel ?: false;
    }

    public function customs(){
    	return $this->belongsToMany(InstitutionUser::class, 'user_role_levels', 'role_level_id', 'custom_id');
    }

    public function role(){
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function belong(){
        return $this->belongsTo(Role::class, 'belongs_to', 'id');
    }
}
