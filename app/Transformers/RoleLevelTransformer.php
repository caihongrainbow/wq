<?php

namespace App\Transformers;

use App\Models\RoleLevel;
use League\Fractal\TransformerAbstract;

class RoleLevelTransformer extends TransformerAbstract
{
    public function transform(RoleLevel $roleLevel)
    {
    	$role = $roleLevel->role;
        return [
            'name' => $role->name,
            'display_name' => $role->display_name, 
            'level' => $roleLevel->level,
            'created_at' => $role->created_at->toDateTimeString(),
            'updated_at' => $role->updated_at->toDateTimeString(),
        ];
    }
}