<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Role;
use App\Models\WechatMp;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\InstitutionRole;

use App\Events\TestEvent;

use DB;
use Auth;
use Log;
use Illuminate\Support\Facades\Redis;

class UsersController extends Controller
{
     use \App\Models\Traits\AuthAccessHelper;

    public function index(){
    	// $user = User::find(2);
    	// var_dump($user->can('edit_settings'));
    	// $wechat = app('wechat.official_account');
    	// var_dump($wechat);
    	return '';
    }

    public function test(Request $request){
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

    public function testuser(){
        session(['uid' => 44]);
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


}
