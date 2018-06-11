<?php

namespace App\Traits;

use App\Models\Institution;
use App\Models\User;
use DB;

trait OAuthorizationHelper
{
	protected $allow_register_types = ['wechat', 'account', 'phone'];

	public $action;

	public function action($action){
		$this->action = $action;
		return $this;
	}

	/**
	 * [facade 注册门面]
	 * @Author   CaiHong
	 * @DateTime 2018-06-06
	 * @return   [type]     [description]
	 */
	public function facade(){
		$args = func_get_args();
		
		$type = current($args);

		$v = in_array($type, $this->allow_register_types);	

		array_shift($args);//去除首个参数

		$method = $type.ucfirst($this->action);

		if(is_callable(self::class, $method))
			return self::$method(...$args);
	}

	/**
	 * [wechatRegister 微信注册]
	 * @Author   CaiHong
	 * @DateTime 2018-06-06
	 * @param    [type]     $wechatUser [description]
	 * @param    [type]     $insid      [description]
	 * @param    [type]     $wechatMpid [description]
	 * @return   [type]                 [description]
	 */
	protected function wechatRegister($wechatUser, $wechatMpid, $insid){
		// dd($wechatUser->name);
		$user = $this->wechatValidate($wechatUser->id, $insid);
		if(!$user){
			DB::beginTransaction();
			$data = ['name' => $wechatUser->name, 'avatar' => $wechatUser->avatar, 'email' => $wechatUser->email];
			
			$user = $this->baseRegister($data, $insid); //添加基本的用户信息

			if($user){ // 
				Institution::find($insid)->userWechatMps()->attach($wechatMpid, ['openid' => $wechatUser->id, 'user_id' => $user->id]);

				DB::commit();
			}else{
				DB::rollback();
			}
		}

		return $user;
	}

	/**
	 * [wechatValidate 微信绑定账号存在验证]
	 * @Author   CaiHong
	 * @DateTime 2018-06-07
	 * @param    [type]     $openid [description]
	 * @param    [type]     $insid  [description]
	 * @return   [type]             [description]
	 */
	protected function wechatValidate($openid, $insid){
		$user_id = Institution::find($insid)->userWechatMps()->where('openid', $openid)->first()->pivot->user_id ?? [];
		return $user_id ? User::find($user_id) : false;
	}

	/**
	 * [baseRegister 基本信息注册]
	 * @Author   CaiHong
	 * @DateTime 2018-06-08
	 * @param    [type]     $user     [description]
	 * @param    [type]     $insid    [description]
	 * @param    [type]     $roleName [description]
	 * @return   [type]               [description]
	 */
	protected function baseRegister($user, $insid, $roleName = null){
		$user = User::create($user);
		is_null($roleName) && $roleName = Institution::find($insid, ['orgid'])->getDefaultRoleName();
		$return = $user->assignRole($roleName);

		return $return ? $user : $return;
	}

	public function login($user){
		$sessionKey = config('session.login_key');
		session([$sessionKey => $user ?? []]);
	}

}