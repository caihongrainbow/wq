<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Venturecraft\Revisionable\RevisionableTrait;

class Institution extends Authenticatable implements JWTSubject
{
    /**
     * 软删除
     */
    use SoftDeletes, RevisionableTrait;
    // 创建操作是否记录
    protected $revisionCreationsEnabled = true;
    // 允许记录的字段
    protected $keepRevisionOf = ['name', 'orgid', 'deleted_at'];
    protected $revisionCleanup = true;
    // 限制某个模型的记录数
    protected $historyLimit = 200;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'orgid', 'type_id', 'parent_id'];

    /**
     * [type 与institution_types关联 反向1对多]
     * @Author   CaiHong
     * @DateTime 2018-06-07
     * @return   [type]     [description]
     */
    public function type(){
    	return $this->belongsTo(InstitutionType::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
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


    public function md5CreateClientid($user_id){
        return substr(md5($this->orgid), 13, 6).substr(md5($this->orgid.(string)$user_id), 7, 18);
    }

    public function profiles(){
        return $this->belongsToMany(InstitutionProfileSetting::class, 'institution_profiles', 'ins_id', 'field_id', 'id', 'field_id')->withPivot(['field_data']);
    }

    public function opens(){
        return $this->morphedByMany(InstitutionType::class, 'model', 'institution_opens', 'ins_id', 'model_id');
    }
}
