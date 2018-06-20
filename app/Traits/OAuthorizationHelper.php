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
	public $custom;

	public $institution;

	public $error;

	protected $allow_register_types = ['wechat', 'account', 'mobile'];

	public $action;

	public function __construct(){
		$this->guard = 'api';
	}

	public function action($action){
		$this->action = $action;
		return $this;
	}

	public function institution($key){

		$by = is_numeric($key) ? 'id' : 'orgid';

		$institution = Institution::where($by, $key)->first() ?? [];

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
		if(!$this->institution) {
			$this->error('不存在此合作机构');
			return;
		}

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
	protected function baseRegister($data, $roleName = null)
	{
		$user = User::create($data);
		is_null($roleName) && $roleName = $this->institution->getDefaultRoleName();
		$return = $user->assignRole($roleName);

		return $return ? $user : $return;
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
		if($user->hasAnyRole(['super', 'admin'])){
			$this->error('此用户禁止远程授权登录');
			return;
		}

		if($create){
			$clientid = $this->institution->md5CreateClientid($user->id);

			$institutionUser = InstitutionUser::firstOrCreate(['user_id' => $user->id, 'ins_id' => $this->institution->id, 'clientid' => $clientid]);

			$this->custom = $institutionUser;
		}else{
			$institutionUser = InstitutionUser::where(['user_id' => $user->id, 'ins_id' => $this->institution->id])->first() ?? [];

			if($institutionUser){
				$this->custom = $institutionUser;
			}else{
				$this->error('用户不存在');
				return;
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
	protected function mobileLogin($phoneNumber, $areaCode)
	{
		return DB::transaction(function () use ($phoneNumber, $areaCode) {

			$phone = Phone::firstOrCreate(['phone_number' => $phoneNumber, 'area_code' => $areaCode]);

			$user = $phone->users()->first();
			
			if(!$user){
				$data = ['name' => substr(md5($phoneNumber.$areaCode),8,16)];

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
               
        if($passport == $user->password){
    		$this->contact($user, false);
        }else{
        	$this->error('用户名或密码错误');
        	return;
        }

	}

	protected function error($message){
		$this->error = $message;
		return;
	}

}