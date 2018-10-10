<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

use App\Transformers\UserTransformer;
use App\Transformers\InstitutionUserTransformer;
use App\Transformers\InstitutionTransformer;
use App\Transformers\RoleTransformer;
use App\Transformers\PhoneTransformer;
use App\Models\InstitutionUser;
use App\Models\Role;

use App\Traits\OAuthorizationHelper;

use Auth;
use Exception;
use JWTAuth;

class ClientsController extends Controller
{
    use OAuthorizationHelper;

    public function index(Request $request)
    {
    	$user = $this->user();

    	$insid = $user->ins_id;

    	if($request->role && $request->level){
            $data = $this->getInstitutionUserByRoleName($insid, $request->role);

            return $this->response->collection($data, new InstitutionUserTransformer);
    	}

    }

    public function byClientidShow(Request $request)
    {
        $user = $this->user();

        $insid = $user->ins_id;

    	$clientid = $request->clientid;

    	$client = $this->updateInstitutionUserByClientid($insid, $clientid);

        if(!$client){
            return $this->response->errorNotFound();
        }
    	return $this->response->item($client, new InstitutionUserTransformer);
    }

    public function byClientidDestroy(Request $request)
    {
        $user = $this->user();

        $insid = $user->ins_id;

        $clientid = $request->clientid;

        $client = $this->getInstitutionUserByClientid($insid, $clientid);

        if(!$client){
            return $this->response->errorNotFound();
        }

        $result = $this->deleteInstitutionUserByClientid($client);

        return $result ? $this->response->noContent() : $this->response->errorInternal();
    }

    public function byMobileShow(Request $request){
        $user = $this->user();

        $insid = $user->ins_id;

        $areaCode = $request->area_code ?? '86';
        $mobile = $request->mobile_number;

        $client = $this->getInstitutionUserByMobile($insid, $areaCode, $mobile);

        if(!$client){
            return $this->response->errorNotFound();
        }
        return $this->response->item($client, new InstitutionUserTransformer);
    }


	public function store(Request $request)
    {
        $phoneNumber = $request->phone_number; 
        $areaCode = $request->area_code ?? '86';
        $name = $request->name;
        $realname = $request->realname;

        //权限验证
        $user = $this->user();

        if(is_null($user)){
            return $this->response->errorUnauthorized('未被授权的用户');
        }

        if(!$user->can('create_client')){
            return $this->response->errorForbidden('您无法享有此权限');
        }

        $this->institution($user->orgid)->action('login')->facade('mobile', $phoneNumber, $areaCode, ['name' => $name, 'realname' => $realname]);

        return $this->response->created();
    }

    public function currentShow(Request $request){
        return $this->response->item($this->user(), new InstitutionUserTransformer);
    }
}
