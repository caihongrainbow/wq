<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
	public function institutions(){
		return $this->belongsToMany(Institution::class, 'institution_roles', 'role_id', 'ins_id');
	}

}
