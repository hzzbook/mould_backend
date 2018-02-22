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
Route::post('/homepage', 'AppbaseController@homepage');

#App登录
Route::post('/login', 'AppbaseController@login');

#App快捷登录
Route::post('/quicklogin', 'AppbaseController@quicklogin');

#获取验证码
Route::post('/get_smsscode', 'AppbaseController@getSmsscode');

#检测验证码
Route::post('/check_smsscode', 'AppbaseController@checkSmsscode');

#手机号是否存在
Route::post('/check_mobile', 'AppbaseController@checkMobile');

#图形验证码
Route::post('/captcha', 'AppbaseController@captcha');

#App注册
Route::post('/register', 'AppbaseController@register');

#忘记密码
Route::post('/forget_password', 'AppbaseController@forgetPassword');

#第三方帐号绑定
Route::post('/third_account', 'UserController@thirdAccount');

#个人资料
Route::post('/userinfo', 'UserController@userinfo');

#保存个人资料
Route::post('/save_userinfo', 'UserController@saveUserinfo');

#上传头像
Route::post('/upload_head', 'UserController@uploadHead');

#更换手机号
Route::post('/change_mobile_smss', 'UserController@changeMobileSmss');
Route::post('/check_mobile_smss', 'UserController@checkMobileSmss');
Route::post('/change_mobile', 'UserController@changeMobile');

#更换密码
Route::post('/change_password', 'UserController@changePassword');

#手机短信更换密码
Route::post('/change_password_smss', 'UserController@changePasswordSmss');
Route::post('/check_password_smss', 'UserController@changePasswordSmss');
Route::post('/change_password_mobile', 'UserController@changePasswordMobile');

#消息列表
Route::post('/messages', 'UserController@messages');

#消息内容
Route::post('/message', 'UserController@message');

#标记消息为已读
Route::post('/message_readed', 'UserController@messageReaded');

#删除消息
Route::post('/message_delete', 'UserController@messageDelete');

Route::post('/articles', 'ContentController@articles');
Route::get('/articles', 'ContentController@articles');
Route::post('/add_article', 'ContentController@addArticle');

#新闻列表
Route::post('/news_list', 'ContentController@newsList');
Route::get('/news_list', 'ContentController@newsList');
#新闻详情
Route::post('/news_item', 'ContentController@newsItem');
Route::get('/news_item', 'ContentController@newsItem');

#手机短信登录
Route::get('/', function () {
    #return view('welcome');
    return 'Hello World';
});

Route::get('dbselect', 'AppController@dbselect');


#网页
Route::get('/service.html', 'AppbaseController@service');
Route::get('/privacy.html', 'AppbaseController@privacy');
Route::get('/helper.html', 'AppbaseController@helper');

Route::post('/feedback', 'AppbaseController@feedback');


#文件上传页面
Route::get('/uploadfile', 'AppController@uploadFile');
Route::post('/uploadFile', 'AppController@uploadHead');


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
