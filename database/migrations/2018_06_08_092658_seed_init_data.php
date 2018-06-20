<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Eloquent\Model;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Role;
use App\Models\WechatMp;
use App\Models\User;

class SeedInitData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $salt = rand(10000, 99999);
        $user = User::create([
            'name' => '超级管理员',
            'account' => 'super',
            'login_salt' => $salt,
            'password' => md5PlusSalt('superadmin', $salt),
        ]);

        $type = InstitutionType::create([
            'title' => '公司',
            'sign' => 'company',
        ]);

        $ins = Institution::create([
            'name' => '御温泉',
            'orgid' => 'wq6eb6ae3a70c8aaeb',
            'type_id' => $type->id,
        ]);

        $roleSuper = Role::create([
            'name' => 'super',
            'display_name' => '超级管理员',
        ]);

        $user->assignRole('super');

        $roleAdmin = Role::create([
            'name' => 'admin',
            'display_name' => '管理员',
        ]);

        $roleUser = Role::create([
            'name' => 'user',
            'display_name' => '用户',
        ]);

        $wechatMp = WechatMp::create([
            'appid' => 'wxbd2d3ea321d9d7a3',
            'appsecret' =>'c7236c957dc1e749f79355d043221917',
        ]);

        $ins->wechatMps()->attach($wechatMp->id, ['is_on' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $ins = Institution::where(['name' => '御温泉'])->first();
        $user = User::where(['account' => 'super'])->first();
        $roleSuper = Role::where(['name' => 'super'])->first();
        $roleUser = Role::where(['name' => 'user'])->first();
        $roleAdmin = Role::where(['name' => 'admin'])->first();
        $wechatMp = WechatMp::where(['appid' => 'wxbd2d3ea321d9d7a3'])->first();
        $ins->wechatMps()->detach($wechatMp->id, ['is_on' => 1]);
        $type = InstitutionType::where(['sign' => 'company'])->first();
        $ins->delete();
        $user->removeRole('super');
        $user->delete();
        $wechatMp->delete();
        $type->delete();
        $roleUser->delete();
        $roleAdmin->delete();
        $roleSuper->delete();
    }
}
