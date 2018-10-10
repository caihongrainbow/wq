<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');


$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'serializer:array'
], function($api) { 

    //登录相关 频率限制，节流，防止攻击
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api) {
        /* 平台相关 */
        // 帐密登录
        $api->post('authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');
        // 手机登录
        $api->post('mobile-authorizations', 'AuthorizationsController@mobileStore')
            ->name('api.authorizations.mobile.store');
        // 微信登录
        $api->get('wechat-authorizations', 'AuthorizationsController@wechatStore')
            ->name('api.authorizations.wechat.store');
        // 刷新token
        $api->put('authorizations', 'AuthorizationsController@update')
            ->name('api.authorizations.update')
            ->middleware('auth.user');
        // 删除token
        $api->delete('authorizations', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy')
            ->middleware('auth.user');

        /* 机构相关 */
        // 帐密登录
        $api->post('institutions/{orgid}/authorizations', 'AuthorizationsController@insStore')
            ->name('api.institutions.authorizations.store');
        // 手机登录
        $api->post('institutions/{orgid}/mobile-authorizations', 'AuthorizationsController@insMobileStore')
            ->name('api.institutions.authorizations.mobile.store');    
        // 微信登录
        $api->get('institutions/{orgid}/wechat-authorizations', 'AuthorizationsController@insWechatStore')
            ->name('api.institutions.authorizations.wechat.store');  
        // 刷新token
        $api->put('institutions/{orgid}/authorizations', 'AuthorizationsController@insUpdate')
            ->name('api.institutions.authorizations.update')
            ->middleware('auth.client');
        // 删除token
        $api->delete('institutions/{orgid}/authorizations', 'AuthorizationsController@insDestroy')
            ->name('api.institutions.authorizations.destroy')
            ->middleware('auth.client');
        
        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        // 图片验证码
        $api->post('captchas', 'CaptchasController@store')
            ->name('api.captchas.store');
    });

    //访问相关 频率限制
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        // 游客可以访问的接口

        // 需要 token 验证的接口
        $api->group(['middleware' => ['auth.client']], function($api) {
            //通过标识ID获取用户信息 
            $api->get('clients/by-clientid', 'ClientsController@byClientidShow')
                ->name('api.clients.by-clientid.show');
            //通过标识ID删除用户 
            $api->delete('clients/by-clientid', 'ClientsController@byClientidDestroy')
                ->name('api.clients.by-clientid.destroy');
            //通过手机号码 
            $api->get('clients/by-mobile', 'ClientsController@byMobileShow')
                ->name('api.clients.by-mobile.show');
            //获取用户列表
            $api->get('clients', 'ClientsController@index')
                ->name('api.clients.index');
            //新增用户
            $api->post('clients', 'ClientsController@store')
                ->name('api.clients.store');

            //获取角色列表
            $api->get('roles', 'RolesController@index')
                ->name('api.roles.index');
            //获取角色信息   ！！
            $api->get('roles/by-name/{name}', 'RolesController@byNameShow')
                ->name('api.roles.by-name.show');
            //新增角色  
            $api->post('roles', 'RolesController@store')
                ->name('api.roles.store');

            //获取个人信息 
            $api->get('clients/current', 'ClientsController@currentShow')
                ->name('api.clients.current.show');
            //修改登录用户  ！！
            $api->patch('clients/current', 'ClientsController@currentUpdate')
                ->name('api.clients.current.update');

            //录入积分
            //扣除积分
            
            //通过ORGID获取组织机构信息
            //通过ORGID修改组织机构
            //通过ORGID删除组织机构
            
            //获取组织机构列表
            $api->get('institutions', 'InstitutionsController@index')
                ->name('api.institutions.index');
            //创建子公司
            $api->post('institutions', 'InstitutionsController@store')
                ->name('api.institutions.store');
            // 图片资源
            // $api->post('images', 'ImagesController@store')
            //     ->name('api.images.store');    
        });

        $api->group(['middleware' => ['auth.user']], function($api) {
            $api->get('users/current', 'UsersController@currentShow')
                ->name('api.users.current.show');
            //创建集团公司
            $api->post('group-institutions', 'InstitutionsController@groupStore')
                ->name('api.institutions.group.store');
        });
    });




});

$api->version('v2', [
    'namespace' => 'App\Http\Controllers\Api\V2'
], function($api) {
    //测试短信验证码
    $api->get('version', function() {
        return response('this is version v2');
    });

    // 短信验证码
    $api->post('verificationCodes', 'VerificationCodesController@store')
        ->name('api.verificationCodes.store');
});
