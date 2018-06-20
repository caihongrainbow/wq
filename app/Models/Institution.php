<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Institution extends Model
{
    /**
     * 软删除
     */
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'orgid', 'type_id'];

    /**
     * [type 与institution_types关联 反向1对多]
     * @Author   CaiHong
     * @DateTime 2018-06-07
     * @return   [type]     [description]
     */
    public function type(){
    	return $this->belongsTo(InstitutionType::class);
    }

    /**
     * [wechatMps 与wechat_mps关联 多对多]
     * @Author   CaiHong
     * @DateTime 2018-06-07
     * @return   [type]     [description]
     */
    public function wechatMps(){
    	return $this->belongsToMany(WechatMp::class, 'institution_wechat_mps', 'ins_id', 'wechat_mp_id')->wherePivot('is_on', 1);;
    }

    /**
     * [roles 与roles关联 多对多]
     * @Author   CaiHong
     * @DateTime 2018-06-07
     * @return   [type]     [description]
     */
    public function roles(){
        return $this->belongsToMany(Role::class, 'institution_roles', 'ins_id', 'role_id');
    }

    /**
     * [users 与users关联 代表用户]
     * @Author   CaiHong
     * @DateTime 2018-06-08
     * @return   [type]     [description]
     */
    public function users(){
        return $this->belongsToMany(User::class, 'institution_users', 'ins_id', 'user_id')->withTimestamps();
    }


    public function getDefaultRoleName(){
        return 'user';
        // return $this->orgid.'_user';
    }

    public function byOrgid($orgid){
        return $this::where('orgid', $orgid)->first();
    }

    public function md5CreateClientid($user_id){
        return substr(md5($this->orgid), 13, 6).substr(md5($this->orgid.(string)$user_id), 7, 18);
    }

}
