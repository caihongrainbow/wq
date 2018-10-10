<?php

namespace App\Observers;

use App\Models\User;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class UserAuthObserver
{
    public function saving(UserAuth $userAuth)
    {
        if($userAuth->phone_id && $userAuth->name){
        	$userAuth->certified_at = date('Y-m-d H:i:s');
        }
    }
}