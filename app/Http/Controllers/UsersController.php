<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;
use App\Models\WechatMp;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\InstitutionRole;
use App\Models\InstitutionUser;
use App\Models\Phone;
use App\Models\Permission;
use App\Models\RoleLevel;
use App\Models\Test;
use App\Models\InstitutionProfileSetting;

use App\Events\TestEvent; 

use DB;
use Auth;
use Log;
use Cache;
use Exception;
use Illuminate\Support\Facades\Redis;

class UsersController extends Controller
{

    public function index(){
    	return '';
    }

    public function test(Request $request)
    {
        for($i=0;$i<100;$i++){
            echo $i;
        }
        // InstitutionUser::find(2)->assignRole('bronze');
        // InstitutionUser::find(3)->roles()->detach();

        // $client = $this->getInstitutionUserByMobile(1, '86', '13726293210');
        // $a = "a:1:{s:4:"shop";a:2:{i:0;i:2;i:1;i:3;}}";
        // dd();
        // $orginal_data = [
        //     'delete_role' => ['shop' => [2, 3]],
        //     'admin_login' => 1,
        //     'manage_role' => ['shop' => [1]],
        // ];
        // $name = ['delete_role', 'admin_login', 'manage_role'];
        // $entity = [
        //     ['shop' => [2] ], //
        //     1, //null
        //     ['shop' => [1]]
        // ];
        // $role = Role::find(3);
        // $role->givePermissionTo($name, $entity);
        // $user = InstitutionUser::find(2);
        // echo $user->can('admin_login');
        
        // dd($user->encrypt("10", "abcd"));
        // $role = Role::find(3);
        // echo $role->hasPermissionTo('delete_role') ?: 0;
        // Permission::create(['name' => 'create_group', 'guard_name' => 'web', 'display_name' => '创建集团公司']);
        // echo $user->can('manage_role') ?: 0;
        // echo $user->can();
        // echo $user->getDirectPermissions();
        // echo $user->getAllPermissions();
        // $user = User::find(3);
        // echo Auth::guard('api')->fromUser($user);
        // echo Auth::guard('test')->fromUser(InstitutionUser::find(2));
        // $ins = Institution::find(1);
        // $phone = InstitutionUser::find(2);
        // echo $phone;
        // echo Auth::guard('api')->fromUser($phone);
        //删除缓存
        // $this->cleanCache('38bb6ef54a929f6b8665c465', 'clientid');
        // InstitutionUser::find(1)->assignRole('super');
        // echo $this->getInstitutionUserByRoleName(1, 'user');
        //创建角色
        // Role::create(['name' => 'edit_role', 'display_name' => '修改角色']);
        //创建权限
        // Permission::create(['name' => 'edit_role', 'display_name' => '修改角色']);
        // Permission::find(1)->delete();
        // 赋予角色权限
        // Role::find(3)->givePermissionTo('delete_role', InstitutionUser::find(2));
        // 移除角色权限
        // Role::find(3)->guard(['api'])->revokePermissionTo('role_manage');
        // echo User::find(1)->can('role_manage') ?: 0;
        // echo Role::orWhereNull('ins_id')->orWhereIn('ins_id', [1])->whereNotIn('name', ['super', 'admin'])->get();
        // echo $this->getInstitutionWithSub(1);
        // $roleName = 'user';
        // $level = 2;
        // $insid = 1;
        // $d = Role::where('name', $roleName)->first()->roleLevels()->where(['level' => $level, 'ins_id' => $insid])->get();
        // echo $d;
        // $roleLevel = $rl->getByLevel(1, 3, 2);
        // dd($roleLevel);
        // $iu = Role::where('name', 'user')->first()->institutionUsers()->where('ins_id', 1)->get();

        // echo $iu;
        // $iu = $client->getByClientid('38bb6ef54a929f6b8665c465');
        // $iu = Institution::find(1);
        // echo gettype($iu);
        // $v = 0;
        // $s = false ?: 'test';
        // $s = false ?? 'test'; //isset false 0
        // dd($s);

        // echo strlen(Institution::find(1)->md5CreateClientid(1));
        // echo strlen('oMhbBjjP0kYNeqGj21pTA7PHJa7s');
        // echo substr(md5('wq6eb6ae3a70c8aaeb'), 13, 6).substr(md5(), 7, 18);
        // $user = User::find(1);
        // dd($user->hasAnyRole(['super', 'admin']));
        // echo User::where(['account' => 'super'])->first();
        // $roles = Role::whereHas('institutions', function($query){
        //     $query->where('ins_id', 2);
        // })->get();
        // $roles = Role::whereDoesntHave('institutions')->where('name', 'user')->get();where('ins_id', 1);
        // $test = User::find(3)->getStoredRole('user');
        // dd($test);
        // Institution::onlyTrashed()->where('id', 1)->restore();
        // echo Institution::find(1)->users()->first();
        // InstitutionUser::create(['user_id' => 22315, 'ins_id' => 66]);
        // $user = Institution::find(1);
        // echo Auth::guard('institutions')->fromUser($user);
        // $cache = \Cache::get('verificationCode_13726293210');
        // echo $cache;
        // echo public_path();
        // $monolog = Log::getMonolog();
        // $monolog->popHandler();
        // echo $this->testfunction();
        // echo bcrypt('123456');
        // var_dump(phoneValidate('+853', '66665956'));
        // $ins = Institution::withTrashed()->find(1);
        // if($ins->trashed()){
        //     $ins->restore();
        // }
        // echo date('Y-m-d H:i:s',time());
        // echo substr(md5('御温泉'.date('YmdHis',time())),8,16);
        // User::find(2)->assignRole();
        // echo Institution::find(66, ['orgid'])->getDefaultRoleName();
        // Institution::find(66)->roles()->attach(3);
        // Role::create(['name' => 'user', 'display_name' => '用户', 'type' => 0]);
        // $user = Institution::find(66)->userWechatMps()->where('openid', 'A')->first() ?? [];
        // dump($user);
        // $user = User::find(2);
        // event(new TestEvent($user));
        // $key = config('session.login_key');
        // echo $key;
        // dd(isLogin());
        // $user = session('wechat.oauth_user') ?? null;
        // if($user){
        //     var_dump($user);
        // }else{
        //     var_dump('no exist');
        // }
    	return null;
    }

    public function testuser(InstitutionUser $client){
        // $client->cleanCache('38bb6ef54a929f6b8665c465', 'clientid');
        // echo Cache::get('institution_user_clientid_38bb6ef54a929f6b8665c465');
        // echo $client->get(1);
        // dd($this->auth->user()); 
        // User::create([
        //     'name' => 'caihong',
        //     'password' => bcrypt('123456'),
        //     'test' => 'test',
        // ]);
        // $ins = Institution::find(66);
        // $pivot = $ins->wechatMps()->where('wechat_mp_id', 1)->first()->pivot;
        // echo $pivot;
        // $ins->wechatMp()->attach(1, ['is_on' => 1]);
        // $wx = WechatMp::find(1);
        // $user = $wx->users;
        // dd(User($wx->users));
        // $user = User::find(2);
        // dd($user->institutions);
        // $user->removeRole('Maintainer');
        // event(new TestEvent($user));
        // $officialAccount = app('wechat.official_account');
        // dd($officialAccount);
        // $user = User::find(2);
        // dd($user->roles);
        // $data = null;
        // $data = Institution::where(['name' => '御温泉', 'parent_id' => 0])->get();
        // //$data = InstitutionType::find(1)->with('institutions')->get();
        // dd($data);
        return null;
    }

    public function dbtest(){
        echo 'dbtest';
//         $cnwq_conn = DB::connection('cnwq');
//         $data = $cnwq_conn->table('user as u')
//                 ->rightJoin('user_group_link as ugl', 'u.uid', '=', 'ugl.uid')
//                 ->leftJoin('user_level as ul', 'u.uid', '=', 'ul.uid')
//                 ->leftJoin('user_credit as uc', 'u.uid', '=', 'uc.user_id')
//                 ->leftJoin('employee as e', 'u.uid', '=', 'e.user_id')
//                 ->select('u.uid', 'u.realname', 'u.phone', 'u.account', 'u.password', 'u.login_salt', 'u.uname', 'u.openid', 'ugl.user_group_id', 
// 'ugl.company_id', 'ugl.identity', 'ul.rule_id', 'uc.history_credit', 'uc.current_credit', 'uc.old_card', 'e.eid', 'e.ename')
//                 ->where('u.is_del', '=', 0)->whereExists()->offset(0)->limit(5)->toSql();
//         dd($data);
        // $data = Institution::onlyTrashed()->get();
        // dd($data);
        // $ins = Institution::find(1);
        // $history = $ins->revisionHistory;
        // dd($history);
        // Log::useFiles(storage_path().'/logs/test.log');
        // Log::info('info', 'test');
    }
}
