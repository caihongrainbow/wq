<?php

namespace App\Transformers;

use App\Models\InstitutionUser;
use League\Fractal\TransformerAbstract;

class InstitutionUserTransformer extends TransformerAbstract
{
    public function transform(InstitutionUser $iu)
    {
        $user = $iu->user;
        $institution = $iu->institution;
        return [
            'ins_id' => $iu->ins_id,
            'user_id' => $iu->user_id,            
            'orgid' => $institution->orgid,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }
}