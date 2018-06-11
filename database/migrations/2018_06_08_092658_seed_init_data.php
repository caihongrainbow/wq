<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Eloquent\Model;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Role;
use App\Models\WechatMp;

class SeedInitData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $type = InstitutionType::create([
            'title' => '公司',
            'sign' => 'company',
        ]);

        $ins = Institution::create([
            'name' => '御温泉',
            'orgid' => makeOrgid('御温泉'),
            'type_id' => $type->id,
        ]);

        $role = Role::create([
            'name' => $ins->orgid.'_user',
            'display_name' => '用户',
        ]);

        $ins->roles()->attach($role->id);

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
        $role = Role::where(['name' => $ins->orgid.'_user'])->first();
        $ins->roles()->detach($role->id);
        $wechatMp = WechatMp::where(['appid' => 'wxbd2d3ea321d9d7a3'])->first();
        $ins->wechatMps()->detach($wechatMp->id, ['is_on' => 1]);
        $type = InstitutionType::where(['sign' => 'company'])->first();
        $ins->delete();
        $wechatMp->delete();
        $type->delete();
    }
}
