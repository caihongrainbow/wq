<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use App\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles;
    use Notifiable;

    protected $institution;

    public function institution($id){
        if(is_numeric($id)){
            $institution = Institution::find($id);
        }

        if(is_string($id)){
            $institution = Institution::where('orgid', $id)->first();
        }

        $this->institution = $institution;

        return $this;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'email', 'password', 'introduction', 'avatar', 'login_salt', 'account'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'account', 'password', 'login_salt', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function institutions(){
        return $this->belongsToMany(Institution::class,'institution_users', 'user_id', 'ins_id');
    }

    /**
     * [getAuthPassword 获取验证的密码和盐]
     *    继承的 Illuminate\Foundation\Auth\User中
     * 使用了trait（Illuminate\Auth\Authenticatable）,
     * 密码加密方式由bcrypt变为加盐的形式
     * 重写trait中的getAuthPassword方法
     * @Author   CaiHong
     * @DateTime 2018-06-09
     * @return   [type]     [description]
     */
    public function getAuthPassword()
    {
        return ['password' => $this->password, 'salt' => $this->login_salt];
    }

    /**
     * [userWechatMps 与wechat_mps、users关联 多对多]
     * @Author   CaiHong
     * @DateTime 2018-06-07
     * @return   [type]     [description]
     */
    public function wechatMps(){
        return $this->belongsToMany(WechatMp::class, 'user_wechat_mps', 'user_id','wechat_mp_id')->withPivot('openid', 'unionid');
    }

    public function attachWechatMp($wechatMpid, $openid, $unionid = null){
        $data = ['openid' => $openid];
        !is_null($unionid) && $data['unionid'] = $unionid;
        $this->wechatMps()->attach($wechatMpid, $data);
    }

    /**
     * [getStoredRole 重写HasRoles中的方法]
     * @Author   CaiHong
     * @DateTime 2018-06-19
     * @param    [type]     $role [description]
     * @return   [type]           [description]
     */
    public function getStoredRole($role) : Role
    {
        if(is_numeric($role)){
            $where = ['id' => $role, 'guard_name' => $this->getDefaultGuardName()];
        }else if(is_string($role)){
            $where = ['name' => $role, 'guard_name' => $this->getDefaultGuardName()];
        }else{
            $where = [];
        }

        if(!is_null($this->institution)){
            $role = Role::whereHas('institutions', function($query){
                $query->where('ins_id', $this->institution->id);
            })->where($where)->first();
        }else{
            $role = Role::whereDoesntHave('institutions')->where($where)->first();
        }

        return $role;
    }
}
