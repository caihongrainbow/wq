<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', function () {
    return view('welcome');
});
Route::get('/session', function(){
	$sessionKey = config('session.login_key');
	
	$session = Session::pull($sessionKey);
	echo $session;
});
Route::get('/testuser', 'UsersController@testuser');
Route::get('/test', 'UsersController@test')->name('test');
/* test start */
//1
// Route::middleware(['checkage'])->group(function() {
// 	Route::get('/test/{age}', 'UsersController@test');
// });
//2
// use App\Http\Middleware\CheckAge;
// Route::get('/test', 'UsersController@test')->middleware(CheckAge::class);
//3
// Route::get('/test', 'UsersController@test')->middleware('checkage');
//4
// Route::group(['middleware' => ['checkage']], function(){
// 	Route::get('/test', 'UsersController@test');
// });
/* test end */

Route::resource('users', 'UsersController');

Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
    Route::get('/userwx', function(){
    	
    });
});

