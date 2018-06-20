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
    // $api->get('authorizations/test', 'UsersController@test');

    // 账号/邮箱 密码登录
    $api->post('authorizations', 'AuthorizationsController@store')
        ->name('api.authorizations.store');

    // 微信登录
    $api->get('authorizations/wechat', 'AuthorizationsController@wechatStore')
        ->name('api.wechat.authorizations.store');   

    //手机验证码登录
    $api->post('authorizations/mobile', 'AuthorizationsController@mobileStore')
        ->name('api.mobile.authorizations.store');

    //用户注册
    $api->post('users', 'UsersController@store')
     ->name('api.users.store');

    //登录相关 频率限制，节流，防止攻击
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api) {

        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');

        // 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');

        // 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');
    });

    //访问相关 频率限制
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        // 游客可以访问的接口

        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {
            // 当前登录平台用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');

            // 编辑登录用户信息
            $api->patch('user', 'UsersController@update')
                ->name('api.user.update');

            // 图片资源
            // $api->post('images', 'ImagesController@store')
            //     ->name('api.images.store');    
        });
    });

    /** Test */

	// // 版本
	// $api->get('version', function() {
 //    	return response('this is version v1');
	// });

	// 图片验证码
	$api->post('captchas', 'CaptchasController@store')
	    ->name('api.captchas.store');

	// // 登录
	// $api->post('authorizations', 'AuthorizationsController@store')
	//     ->name('api.authorizations.store');





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
