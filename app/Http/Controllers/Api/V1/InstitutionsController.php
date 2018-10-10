<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

use App\Models\Institution;

class InstitutionsController extends Controller
{
    /**
     * [groupStore 创建集团公司]
     * @Author   CaiHong
     * @DateTime 2018-07-20
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function groupStore(Request $request)
    {
        $user = $this->user();

        if(!$user->can('create_group')){
            return $this->response->errorForbidden('您无法享有此权限');
        }

    	$name = $request->name;
    	$contact_mobile = $request->contact_mobile;
    	$contact_email = $request->contact_email;

    	$account = $request->account;

        $initInsTypes = $request->init_ins_types;

    	$this->createInstitution($name, $contact_mobile, $contact_email, $account, 0, 'company', $initInsTypes);

        $this->response->created();
    }

    public function store(Request $request)
    {
        $user = $this->user();

        $type = $request->type;

        $ability = 'create_'.$type;

        if(!$user->can($ability)){
            return $this->response->errorForbidden('您无法享有此权限');
        }
        dd($user);
        $name = $request->name;
        $contact_mobile = $request->contact_mobile;
        $contact_email = $request->contact_email;
        $account = $request->account;
        $type = $request->type;
        $parent = $request->parent;
        $initInsTypes = $request->init_ins_types;

        $this->createInstitution($name, $contact_mobile, $contact_email, $account, $parent, $type, $initInsTypes);

        $this->response->created();
    }

    public function index(){
        
    }
}
