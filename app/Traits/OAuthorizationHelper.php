<?php

namespace App\Traits;

use App\Models\Institution;
use App\Models\User;
use App\Models\WechatMp;
use App\Models\InstitutionUser;
use App\Models\Phone;

use EasyWeChat\Factory;

use DB;

trait OAuthorizationHelper
{
	public $user;

	public $client;

	public $institution;

	protected $allow_register_types = ['wechat', 'account', 'mobile'];

	public $action;

	public $method;

	public function action($action){
		$this->action = $action;
		return $this;
	}

	public function institution($key){

		$by = is_numeric($key) ? 'id' : 'orgid';

		$institution = Institution::where($by, $key)->first() ?? [];

		if(!$institution) {
			$this->error('不存在此合作机构');
		}

		$this->institution = $institution;

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

		if(is_callable(self::class, $method)){
			$this->method = $method;
			return self::$method(...$args);
		}
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
	protected function wechatLogin($wechatUser, $wechatMpid){

		return DB::transaction(function () use ($wechatUser, $wechatMpid) {

			$user = $this->wechatValidate($wechatMpid, $wechatUser['openid'], $wechatUser['unionid']);

			if(!$user){
				$data = ['name' => $wechatUser['nickname'], 'avatar' => $wechatUser['headimgurl']];
				
				$user = $this->baseRegister($data); //添加基本的用户信息

				if($user){ 
					$user->attachWechatMp($wechatMpid, $wechatUser['openid'], $wechatUser['unionid']);
				}

			}

			$this->contact($user);
		});
	}
	

	/**
	 * [wechatValidate 微信绑定账号存在验证]
	 * @Author   CaiHong
	 * @DateTime 2018-06-13
	 * @param    [type]     $insid   [description]
	 * @param    [type]     $openid  [description]
	 * @param    [type]     $unionid [description]
	 * @return   [type]              [description]
	 */
	protected function wechatValidate($wechatMpid, $openid, $unionid = null){

		$wechatMp = WechatMp::find($wechatMpid);
		$user = $wechatMp->users()->where('openid', $openid)->first();
		
		if($user){
			$user_id = $user->id;
			
			if(!is_null($unionid) && is_null($user->pivot->unionid)){
				$wechatMp->users()->updateExistingPivot($user_id, ['unionid' => $unionid]);
			}
		}

		
		return isset($user_id) ? User::find($user_id) : false;
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
	protected function baseRegister($data, $role = null)
	{
		$user = User::create($data);

		!is_null($role) && $user->assignRole($role);		

		return $user ?: false;
	}

	/**
	 * [contact 将用户与机构相关联]
	 * @Author   CaiHong
	 * @DateTime 2018-06-19
	 * @param    [type]     $userId [description]
	 * @return   [type]             [description]
	 */
	protected function contact($user, $create = true)
	{
		if($this->institution){ // 机构用户
			//不存在用户时是否自动进行创建
			if($create){ //自动创建
				$clientid = $this->institution->md5CreateClientid($user->id);

				$institutionUser = InstitutionUser::firstOrCreate(['user_id' => $user->id, 'ins_id' => $this->institution->id, 'clientid' => $clientid]);

				if($user->realname){
					$institutionUser->name = $user->realname;
					if($institutionUser->phone_id && is_null($institutionUser->name)){
						$institutionUser->certified_at = date('Y-m-d H:i:s', time());
					} 
					$institutionUser->save();
				} 

				if($institutionUser->roles->isEmpty()){
					$roleName = $this->institution->getDefaultRoleName();
					$institutionUser->assignRole($roleName);
				}
				
			}else{ //不创建
				$institutionUser = InstitutionUser::where(['user_id' => $user->id, 'ins_id' => $this->institution->id])->first() ?? [];

				if(!$institutionUser){
					$this->error('用户不存在');
					return;
				}
			}

			$this->client = $institutionUser;			
		}else{ // 平台用户
			if($user instanceof User){
				$this->user = $user;
			}
		}

	}

	/**
	 * [mobileLogin 手机验证码登录]
	 * @Author   CaiHong
	 * @DateTime 2018-06-19
	 * @param    [type]     $phoneNumber [description]
	 * @param    [type]     $areaCode    [description]
	 * @return   [type]                  [description]
	 */
	protected function mobileLogin($phoneNumber, $areaCode, $data = null)
	{
		return DB::transaction(function () use ($phoneNumber, $areaCode) {

			$phone = Phone::firstOrCreate(['phone_number' => $phoneNumber, 'area_code' => $areaCode]);

			$user = $phone->users()->first();
			
			if(!$user){
				empty($data) && $data = ['name' => substr(md5($phoneNumber.$areaCode),8,16)];

				$user = $this->baseRegister($data);

				$phone->users()->attach($user->id);
			}

			$this->contact($user);

		});
	}

	/**
	 * [passportLogin 账号密码登录]
	 * @Author   CaiHong
	 * @DateTime 2018-06-19
	 * @param    [type]     $username [description]
	 * @param    [type]     $password [description]
	 * @return   [type]               [description]
	 */
	protected function passportLogin($username, $password)
	{
		filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['account'] = $username;

        $user = User::where($credentials)->first();

        if(!$user){
        	$this->error('用户不存在');
        	return;
        }

        $passport = md5PlusSalt($password, $user->login_salt);
               
        if(hash_equals($passport, $user->password)){
    		$this->contact($user, false);
        }else{
        	$this->error('用户名或密码错误');
        	return;
        }

	}

	//未写完 ！！
	// protected function passportRegister($account, $password = null, $name = null){
	// 	$credentials['account'] = $account;
	// 	$user = User::where($credentials)->first();
 //        if($user){
 //        	$this->error('此用户账号已被使用');
 //        	return;
 //        }
	// }

	protected function error($message){
		$this->error = $message;
		return;
	}


}