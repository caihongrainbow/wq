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
use App\Models\WechatMp;

class AuthorizationsController extends Controller
{

    protected $error;

	use \App\Traits\OAuthorizationHelper;

    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;
        $password = $request->password;

        $this->action('login')->facade('passport', $username, $password);

        if(!$this->user)    return $this->response->errorUnauthorized($this->error); 
        
        $data = [
            'access_token' => Auth::guard('user')->fromUser($this->user),
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('user')->factory()->getTTL() * 60,
            'id' => $this->user->id,
        ];

        return $this->response->array($data)->setStatusCode(201);
    }

    public function mobileStore(AuthorizationRequest $request)
    {
        $verifyCode = $request->verification_code;
        $phoneNumber = $request->phone_number;
        $areaCode = $request->area_code;

        $cacheVerifyCode = \Cache::get('verificationCode_'.$phoneNumber);
        
        // if(is_null($cacheVerifyCode) || ($verifyCode != $cacheVerifyCode)){
        //     return $this->response->errorUnauthorized('手机验证码错误');
        // }

        $this->action('login')->facade('mobile', $phoneNumber, $areaCode);

        $data = [
            'access_token' => Auth::guard('user')->fromUser($this->user),
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('user')->factory()->getTTL() * 60,
            'id' => $this->user->id
        ];
        return $this->response->array($data)->setStatusCode(201);
    }

    public function wechatStore(AuthorizationRequest $request){
        $config = config(\sprintf('wechat.official_account.%s', $request->account), []);
        $officialAccount = Factory::officialAccount($config);
        $scopes = ['snsapi_userinfo'];

        if ($request->has('code')) {
            $wechatUser = $officialAccount->oauth->user();
 
            $this->facade('wechat', $wechatUser->getOriginal(), $wechatmps->id);

            $data = [
                'access_token' => Auth::guard('user')->fromUser($this->user),
                'token_type' => 'Bearer',
                'expires_in' => Auth::guard('user')->factory()->getTTL() * 60,
                'id' => $this->user->id
            ];
            return $this->response->array($data)->setStatusCode(201);
        } 

        return $officialAccount->oauth->scopes($scopes)->redirect($request->fullUrl());
    }


    public function insStore(AuthorizationRequest $request)
    {
        $username = $request->username;
        $password = $request->password;

        $this->institution($request->orgid)->action('login')->facade('passport', $username, $password);

        if(!$this->client)    return $this->response->errorUnauthorized($this->error); 
        
        $data = [
            'access_token' => Auth::guard('client')->fromUser($this->client),
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('client')->factory()->getTTL() * 60,
            'id' => $this->client->user_id,
            'clientid' => $this->client->clientid
        ];
        return $this->response->array($data)->setStatusCode(201);
    }

    public function insWechatStore(AuthorizationRequest $request){

        $this->institution($request->orgid);

        $wechatmps = $this->institution->wechatMps()->first();
        $config = ['app_id' => $wechatmps->appid, 'secret' => $wechatmps->appsecret];
        $officialAccount = Factory::officialAccount($config);
        $scopes = ['snsapi_userinfo'];

        if ($request->has('code')) {
            $wechatUser = $officialAccount->oauth->user();
 
            $this->action('login')->facade('wechat', $wechatUser->getOriginal(), $wechatmps->id);

            $data = [
                'access_token' => Auth::guard('client')->fromUser($this->client),
                'token_type' => 'Bearer',
                'expires_in' => Auth::guard('client')->factory()->getTTL() * 60,
                'id' => $this->client->user_id,
                'clientid' => $this->client->clientid
            ];
            return $this->response->array($data)->setStatusCode(201);
        } 

        return $officialAccount->oauth->scopes($scopes)->redirect($request->fullUrl());
    }

    public function insMobileStore(AuthorizationRequest $request)
    {
        $verifyCode = $request->verification_code;
        $phoneNumber = $request->phone_number;
        $areaCode = $request->area_code;

        $cacheVerifyCode = \Cache::get('verificationCode_'.$phoneNumber);
        
        // if(is_null($cacheVerifyCode) || ($verifyCode != $cacheVerifyCode)){
        //     return $this->response->errorUnauthorized('手机验证码错误');
        // }

        $this->institution($request->orgid)->action('login')->facade('mobile', $phoneNumber, $areaCode);

        $data = [
            'access_token' => Auth::guard('client')->fromUser($this->client),
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('client')->factory()->getTTL() * 60,
            'id' => $this->client->user_id,
            'clientid' => $this->client->clientid
        ];
        return $this->response->array($data)->setStatusCode(201);
    }

	//刷新第三方登录token
	public function update()
	{
        dd($this->guard);
        $user = $this->user();
	    $token = Auth::guard($this->guard)->refresh();
	    $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $this->factory()->getTTL() * 60,
            'id' => $user->id,
        ];
        return $this->response->array($data)->setStatusCode(201);
	}

    public function insUpdate()
    {
        $client = $this->user();
        $token = Auth::guard($this->guard)->refresh();
        $data = [
            'access_token' => Auth::guard($this->guard)->fromUser($this->client),
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard($this->guard)->factory()->getTTL() * 60,
            'id' => $client->user_id,
            'clientid' => $client->clientid
        ];
        return $this->response->array($data)->setStatusCode(201);
    }

    //删除第三方登录token
    public function destroy()
    {
        Auth::guard($this->guard)->logout();
        return $this->response->noContent();
    }

	//删除第三方登录token
	public function insDestroy()
	{
	    Auth::guard($this->guard)->logout();
	    return $this->response->noContent();
	}


}
