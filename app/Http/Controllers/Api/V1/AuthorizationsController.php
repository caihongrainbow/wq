<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\VerificationCodeRequest;

use Auth;
use Tymon\JWTAuth\JWTAuth;
use EasyWeChat\Factory;

use App\Models\User;

class AuthorizationsController extends Controller
{
    protected $guard;
	use \App\Traits\OAuthorizationHelper;

    public function store(AuthorizationRequest $request)
    {
    	// return $request;
        $username = $request->username;
        $password = $request->password;

        $this->institution($request->orgid)->action('login')->facade('passport', $username, $password);

        return $this->respondWithToken();
    }

    protected function respondWithToken()
	{
        if(!$this->custom){
            return $this->response->errorUnauthorized($this->error);
        }

	    return $this->response->array([
	        'access_token' => Auth::guard($this->guard)->fromUser($this->custom),
            'clientid' => $this->custom->clientid,
	        'token_type' => 'Bearer',
	        'expires_in' => Auth::guard($this->guard)->factory()->getTTL() * 60
	    ])->setStatusCode(201);
	}

	//刷新第三方登录token
	public function update()
	{
	    $token = Auth::guard('api')->refresh();
	    return $this->respondWithToken($token);
	}

	//删除第三方登录token
	public function destroy()
	{
	    Auth::guard('api')->logout();
	    return $this->response->noContent();
	}

	public function wechatStore(Request $request){

        $this->institution($request->orgid);

        $wechatmps = $this->institution->wechatMps()->first();
        $config = ['app_id' => $wechatmps->appid, 'secret' => $wechatmps->appsecret];
        $officialAccount = Factory::officialAccount($config);
        $scopes = ['snsapi_userinfo'];

        if ($request->has('code')) {
        	$wechatUser = $officialAccount->oauth->user();
 
            $user = $this->action('login')->facade('wechat', $wechatUser->getOriginal(), $wechatmps->id);

            return $this->respondWithToken();
        } 

        return $officialAccount->oauth->scopes($scopes)->redirect($request->fullUrl());
	}

	public function mobileStore(VerificationCodeRequest $request)
    {
        $verifyCode = $request->verify_code;
        $phoneNumber = $request->phone_number;
        $areaCode = $request->area_code;

        $cacheVerifyCode = \Cache::get('verificationCode_'.$phoneNumber);
        
        // if(is_null($cacheVerifyCode) || ($verifyCode != $cacheVerifyCode)){
        //     return $this->response->errorInternal('手机验证码错误');
        // }

        $this->institution($request->orgid)->action('login')->facade('mobile', $phoneNumber, $areaCode);

        return $this->respondWithToken();
	}


}
