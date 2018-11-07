<?php

namespace App\Traits;

use App\Models\Institution;
use App\Models\InstitutionUser;
use App\Models\InstitutionType;
use App\Models\InstitutionProfileSetting;
use App\Models\Role;
use App\Models\RoleLevel;
use App\Models\Phone;
use App\Models\User;


use Cache;
use DB;

trait DataAccessHelper
{
	protected $cache = [
    ];

    /**
     * [getCacheKey 获取缓存对应KEY值]
     * @Author   CaiHong
     * @DateTime 2018-06-23
     * @param    [type]     $key    [description]
     * @param    string     $column [description]
     * @return   [type]             [description]
     */
    public function getCacheKey($key, $column)
    {
        return in_array($column, $this->cache) ? $this->cache[$column].'_'.$key : $column.'_'.$key;
    }

    /**
     * [cleanCache 清除缓存]
     * @Author   CaiHong
     * @DateTime 2018-06-23
     * @param    [type]     $key    [description]
     * @param    string     $column [description]
     * @return   [type]             [description]
     */
    public function cleanCache($key, $column)
    {
        Cache::forget($this->getCacheKey($key, $column));
    }

    /**
     * [getInstitutionUserByClientid 通过clientid获取机构用户]
     * @Author   CaiHong
     * @DateTime 2018-06-23
     * @param    [type]     $clientid [description]
     * @return   [type]               [description]
     */
    public function getInstitutionUserByClientid($insid, $clientid)
    {
        return InstitutionUser::where(['clientid' => $clientid, 'ins_id' => $insid])->first() ?? [];
    }

    /**
     * [getInstitutionUserByMobile 通过手机号码获取机构用户]
     * @Author   CaiHong
     * @DateTime 2018-07-16
     * @param    [type]     $insid    [description]
     * @param    [type]     $areaCode [description]
     * @param    [type]     $mobile   [description]
     * @return   [type]               [description]
     */
    public function getInstitutionUserByMobile($insid, $areaCode, $mobile)
    {
        $phone = Phone::where(['phone_number' => $mobile, 'area_code' => $areaCode])->first();
        
        if(!$phone) return false;

        $user = $phone->users()->first();
        return $user ? InstitutionUser::where(['ins_id' => $insid, 'user_id' => $user->id])->first() : false;
    }

    /**
     * [deleteInstitutionUser description]
     * @Author   CaiHong
     * @DateTime 2018-07-16
     * @param    [numeric|string|object]     $client [description]
     * @return   [type]             [description]
     */
    public function deleteInstitutionUser($client) : boolean
    {
        if(is_string($client))
        {
            return InstitutionUser::where(['clientid' => $client])->delete() ? true : false;
        }

        if(is_numeric($client))
        {
            return InstitutionUser::find($client)->delete() ? true : false;
        }

        if($client instanceof InstitutionUser){
            return $client->delete() ? true : false;
        }

        return false;
    }

	/**
	 * [getByRoleLevel 获取指定角色等级的机构用户列表]
	 * @Author   CaiHong
	 * @DateTime 2018-06-25
	 * @param    [type]     $insid    [description]
	 * @param    [type]     $roleName [description]
	 * @param    [type]     $level    [description]
	 * @return   [type]               [description]
	 */
    public function getInstitutionUserByRoleName($insid, $roleName)
    {
        return InstitutionUser::whereHas('roles', function($query) use ($roleName, $insid) {
            $query->where('name', $roleName)->where(function($query) use ($insid){
                $query->whereNull('ins_id')->orWhere('ins_id', $insid);
            });
        })->with(['user'])->get();
    }

    /**
     * [getInstitutionWithSub 获取某机构下的所有机构]
     * @Author   CaiHong
     * @DateTime 2018-06-27
     * @param    [type]     $insids [description]
     * @return   [type]             [description]
     */
    public function getInstitutionWithSub($insids)
    {
        static $data = [];

        empty($data) && $data = [Institution::find($insids)];
        
        if(is_numeric($insids)){
            $ins = Institution::where('parent_id', $insids)->get() ?? [];
        }else{
            $ins = Institution::whereIn('parent_id', $insids)->get() ?? [];
        }
        
        if(!$ins->isEmpty()){; 
            $data = $ins->merge($data);

            $this->getInstitutionWithSub($ins->pluck('id'));
        }

        return collect($data);
    }

    /**
     * [getRoles 获取机构下的角色列表]
     * @Author   CaiHong
     * @DateTime 2018-06-27
     * @param    [type]     $insid [description]
     * @return   [type]            [description]
     */
    public function getRoles($insid)
    {
        $insids = $this->getInstitutionWithSub($insid);
        
        $roles = Role::with('levels')->where(function($query) use ($insids) {
            $query->whereNull('ins_id')->orWhereIn('ins_id', $insids->pluck('id'));
        })->whereDoesntHave('contact')->get();

        return $roles;
    }

    /**
     * [getRole description]
     * @Author   CaiHong
     * @DateTime 2018-07-16
     * @param    [type]     $insid [description]
     * @param    [numeric|string]     $role  [description]
     * @return   [type]            [description]
     */
    public function getRole($insid, $role)
    {
        $insids = $this->getInstitutionWithSub($insid);

        $condition = Role::where(function($query) use ($insids) {
            $query->whereNull('ins_id')->orWhereIn('ins_id', $insids->pluck('id'));
        })->whereNotIn('name', ['super', 'admin']);

        if(is_numeric($role)){
            $role = $condition->where('id', $role)->with(['contact'])->first();
        }

        if(is_string($role)){
            $role = $condition->where('name', $role)->first();
        }

        return $role;
    }

    public function getInstitutionType($type) : InstitutionType
    {
        if(is_numeric($type)){
            $type = InstitutionType::find($type);
        }
        if(is_string($type)){
            $type = InstitutionType::where(['sign' => $type])->first();
        }
        return $type;
    }

    public function getInstitution($ins) : Institution
    {
        if(is_numeric($ins)){
            $ins = Institution::find($ins);
        }
        if(is_string($ins)){
            $ins = Institution::where(['orgid' => $ins])->first();
        }
        return $ins;
    }

    public function createInstitution($name, $mobile, $email, $account, $parent, $type, $initTypes = null)
    {
        return DB::transaction(function () use ($name, $mobile, $email, $account, $parent, $type, $initTypes) {

        
            //数据处理
            $type = $this->getInstitutionType($type);
            $insid = !empty($parent) ? $this->getInstitution($parent)->id : 0;
            
            $orgid = makeOrgid($name);

            $institution = ['name' => $name, 'orgid' => $orgid, 'parent_id' => $insid, 'type_id' => $type->id];
            $institution = Institution::create($institution);

            $settings = InstitutionProfileSetting::all();
            $fki = $settings->keyBy('field_key')->map(function($item, $key){
                return $item->field_id;
            });
            //保存联系人电话
            !empty($mobile) && $institution->profiles()->attach($fki['contact_tel'], ['field_data' => $mobile]);
            //保存联系人邮箱
            !empty($email) && $institution->profiles()->attach($fki['contact_email'], ['field_data' => $email]);

            //写入公司配置数据栏institution_profile
            if($type->sign == 'company'){
                //保存公司标志
                $sign = date('ymd',time()).$institution->id;
                $institution->profiles()->attach($fki['ins_sign'], ['field_data' => $sign]);
                //保存后台地址，较之前做出一些改变，不再存储完整URL路径
                $adminUrl = 'admin/Index/index';
                $institution->profiles()->attach($fki['admin_login_url'], ['field_data' => $adminUrl]);
                //保存前台地址，较之前做出一些改变，不再存储完整URL路径
                $publicUrl = 'public/Personal/shopIndex';
                $institution->profiles()->attach($fki['public_login_url'], ['field_data' => $publicUrl]);
                //$initTypes是sign字段的数组
                if(!is_null($initTypes)){
                    //获取对应的id数组
                    $initTypes = InstitutionType::whereIn('sign', array_unique($initTypes))->get();

                    if($initTypes->isNotEmpty()){
                        $institution->opens()->attach($initTypes);
                    }
                }
                //添加管理员账号
                $user = $this->createAdmin($institution->id, $name.'管理员', $account);
                //保证实例化成功
                $institution->is_init = 1;
                $institution->save();
            }
            

        });
    }

    public function createAdmin($insid, $name, $account, $password = null)
    {
        //检查账号是否可创建
        if($account){
            $user = User::where(['account' => $account])->first();
            if($user){
                return false;
            }
        }

        return DB::transaction(function () use ($insid, $name, $account, $password) {
            $loginSalt = rand(10000, 99999);
            is_null($password) && $password = substr(md5($account.time()), rand(0, 23), 8);
            $passport = md5PlusSalt($password, $loginSalt);
            $user = User::create(['name' => $name, 'account' => $account, 'login_salt' => $loginSalt, 'password' => $passport]);
            $clientid = Institution::find($insid)->md5CreateClientid($user->id);
            $client = InstitutionUser::create(['user_id' => $user->id, 'ins_id' => $insid, 'clientid' => $clientid, 'is_admin' => 1]);
            $client->assignRole('admin');
        });

    }

}