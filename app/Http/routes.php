<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::post('/', function () {
    #return view('welcome');
    return 'Hello World';
});

#App启动页
Route::post('/startup', 'AppController@startup');

#主页数据
Route::post('/homepage', 'AppController@homepage');

#上拉加载更多
Route::post('/pullup', 'AppController@pullup');

#下拉刷新
Route::post('/pulldown', 'AppController@pulldown');

#App登录
Route::post('/login', 'AppController@login');

#App登录
Route::post('/quicklogin', 'AppController@quicklogin');

#获取验证码
Route::post('get_Smsscode', 'AppController@getSmsscode');

#App注册
Route::post('/register', 'AppController@register');

#个人资料
Route::post('/userinfo', 'UserController@userinfo');

#保存个人资料
Route::post('/save_userinfo', 'UserController@saveUserinfo');

#上传头像
Route::post('/upload_head', 'UserController@uploadHead');

#第三方帐号绑定
Route::post('/third_account', 'UserController@thirdAccount');

#更换手机号
Route::post('/change_mobile', 'UserController@changeMobile');

#消息列表
Route::post('/messages', 'UserController@messages');

#消息内容
Route::post('/message', 'UserController@message');

#标记消息为已读
Route::post('/message_readed', 'UserController@messageReaded');

#删除消息
Route::post('/message_delete', 'UserController@messageDelete');

#手机短信登录
Route::get('/', function () {
    #return view('welcome');
    return 'Hello World';
});

Route::get('dbselect', 'AppController@dbselect');


#网页
Route::get('/service.html', 'AppController@service');
Route::get('/privacy.html', 'AppController@privacy');
Route::get('/helper.html', 'AppController@helper');

Route::post('/feedback', 'AppController@feedback');






/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
