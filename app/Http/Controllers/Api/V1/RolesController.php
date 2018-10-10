<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

use App\Transformers\RoleTransformer;
use App\Models\Role;

use Auth;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->user();

    	$insid = $user->ins_id;

    	if(!$user->can('manage_role')){
    		return $this->response->errorForbidden('您无法享有此权限');
    	}

    	$roles = $this->getRoles($insid);

        return $this->response->collection($roles, new RoleTransformer($insid));
    }

    public function show(Request $request)
    {
    	$user = $this->user();
        
        $insid = $user->ins_id;

        if(!$user->can('manage_role')){
            return $this->response->errorForbidden('您无法享有此权限');
        }

        $role = $this->getRole($insid, $request->role);

        return $this->response->item($role, new RoleTransformer($insid));
    }

    public function store(Request $request)
    {
        $user = $this->user();
        
        $insid = $user->ins_id;

        if(!$user->can('create_role')){
            return $this->response->errorForbidden('您无法享有此权限');
        }
        
        Role::create(['name' => $request->name, 'display_name' => $request->display_name, 'ins_id' => $insid]);

        return $this->response->created();
    }

    public function destroy(Request $request)
    {
        $user = $this->user();

        $insid = $user->ins_id;

        if(!$user->can('delete_role')){
            return $this->response->errorForbidden('您无法享有此权限');
        }
        
        $role = Role::where(['name' => $request->name, 'ins_id' => $insid])->first();

        if($role){
            $role->delete();
            return $this->response->noContent();
        }else{
            return $this->response->errorNotFound();
        } 
    }

    public function byNameShow(Request $request){
        $user = $this->user();
        
        $insid = $user->ins_id;

        if(!$user->can('manage_role')){
            return $this->response->errorForbidden('您无法享有此权限');
        }

        $role = $this->getRole($insid, $request->name);

        return $this->response->item($role, new RoleTransformer($insid));
    }
}
