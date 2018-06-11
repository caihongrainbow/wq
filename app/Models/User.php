<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'email', 'password', 'introduction', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'login_salt', 'remember_token',
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

    public function test(){
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                return $this->getStoredRole($role);
            })
            ->each(function ($role) {
                $this->ensureModelSharesGuard($role);
            })
            ->all();
        dd($roles);
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
}
