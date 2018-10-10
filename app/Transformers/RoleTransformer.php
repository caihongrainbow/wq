<?php

namespace App\Transformers;

use App\Models\Role;
use App\Models\RoleLevel;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
    protected $insid = null;

    public function __construct($insid){
        $this->insid = $insid;
    }
    // protected $defaultIncludes = ['levels'];

    protected $availableIncludes = ['levels'];

    public function transform(Role $role)
    {
        $roleLevel = $role->contact;
        $data = [
            'name' => $role->name,
            'display_name' => $role->display_name,
            'allow_update' => $role->ins_id ? true : false,  
            'created_at' => $role->created_at->toDateTimeString(),
            'updated_at' => $role->updated_at->toDateTimeString(),
        ];

        if($roleLevel){
            $belongs = $roleLevel->belong;
            
            $belongsData = [
                'level' => $roleLevel->level,
                'name' => $belongs->name,
                'display_name' => $belongs->display_name,
            ];
            $data['belongs_to'] = $belongsData;
        }

        return $data;
    }

    public function includeLevels(Role $role)
    {
        if($this->insid){
            $levels = $role->levels()->where('ins_id', $this->insid)->get();
        }else{
            $levels = $role->levels;
        }
        
        return $this->collection($levels, new RoleLevelTransformer);
    }
}