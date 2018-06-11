<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\AuthorizationRequest;
use Session;
use Auth;
use EasyWeChat\Factory;
use App\Models\Institution;
use App\Models\User;

class AuthorizationsController extends Controller
{
	use \App\Traits\OAuthorizationHelper;

    public function store(AuthorizationRequest $request)
    {
    	// return $request;
        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['account'] = $username;

        $credentials['password'] = $request->password;
        
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return $this->response->errorUnauthorized('用户名或密码错误');
        }

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    protected function respondWithToken($token)
	{
	    return $this->response->array([
	        'access_token' => $token,
	        'token_type' => 'Bearer',
	        'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
	    ]);
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
		$ins = Institution::where('orgid', $request->orgid)->first();
        $wechatmps = $ins->wechatMps()->first();
        $config = ['app_id' => $wechatmps->appid, 'secret' => $wechatmps->appsecret];
        $officialAccount = Factory::officialAccount($config);
        $scopes = ['snsapi_userinfo'];

        if ($request->has('code')) {
            $user = $this->action('register')->facade('wechat', $officialAccount->oauth->user(), $wechatmps->id, $ins->id);
            
            $token = Auth::guard('api')->fromUser($user);

            return $this->respondWithToken($token)->setStatusCode(201);
        } 

        return $officialAccount->oauth->scopes($scopes)->redirect($request->fullUrl());
	}

	public function add(){
		Session::put('uid', 4);
	}

	public function get(){
		echo Session::pull('uid') ?? 0;
	}
}
