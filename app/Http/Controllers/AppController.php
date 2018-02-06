<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\App_ad;
use App\Model\App_model;
use App\Model\User_model;

#todo 分享功能
class AppController extends Controller
{

    var $app_model;

    public function __construct()
    {
        $this->app_model = new \App\Model\App_model();
        header('Access-Control-Allow-Origin:*');
    }

    #验证不为空
    public function checkNoBlank()
    {

    }

    #创建Token
    public function ceateToken($stats)
    {

    }

    public function uuid()
    {
        return md5(uniqid(md5(microtime(true)),true));
    }

    #验证Token
    public function checkToken($token, $stats)
    {
        $stats = serialize($stats);
        $token_key = md5($stats);
        $token_value = Redis::get($token_key);
        if ($token != '' && $token == $token_value) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    #生成短信验证码
    public function createCode($length)
    {
        return mt_rand(100000,999999);
    }

    #通过Token获取用户信息
    public function getUserByToken($token)
    {
        $userId = Redis::get($token);
        if ($userId != '') {
            return $userId;
        } else {
            return FALSE;
        }
    }

    /**
     * 记录用户信息
     * client_id    客户端ID
     * ip           IP地址
     * lbs          地址坐标
     * platform     平台类型    ios, andorid, wxapp,wxmp,
     * appversion   app版本
     * mobileinfo   手机型号
     * token        访问令牌
     *
     *
     */
    public function mark()
    {

    }

    #启动页
    public function startup(Request $request)
    {
        $token = $request->input('token');
        $version = $request->input('version');
        $platform = $request->input('platform');
        $stats = $request->input('platform');

        $stat = array(
            'version' => $version,
            'platform' => $platform
        );

        if ($this->checkToken($token, $stats)) {

        }

        $model = new \App\Model\App_ad();
        $ad_data = $model->ad();
        if ($ad_data === FALSE) {
            $view_data = array(
                'status' => 'false',
                'code' => '404'
            );
        } else {
            /*$view_data = array(
                'status' => 'true',
                'code'   => '0',
                'ad_data'=> $ad_data
            );*/
            $view_data = array(
                'status' => 'true',
                'code' => '0'
            );
            $view_data = array_merge( $ad_data, $view_data);
        }
        echo json_encode($view_data);
    }

    /**
     * 广告中转
     * 用于统计广告点击率
     */
    public function adTrain(Request $request)
    {
        $ad_id = $request->input('ad_id');

    }

    #主页数据
    public function homepage(Request $request)
    {
        $token = $request->input('token');
        $App_model = new \App\Model\App_model();
        $banner_data = $App_model->banners();


        $view_data = array(
            'status' => 'true',
            'code'   => '0',
            'banner' => $banner_data,
            'new_skin' => 'false',   #是否有新皮肤
            'little_activity' => array(
                'cover' => '',
                'url' => '',
            ),
        );


        $activity_data = $App_model->activity();
        if ($activity_data !== FALSE) {
            if (Redis::get('activity_'.$token) == 1){

            } else {
                $view_data['activity'] = $activity_data;
                Redis::set('activity_'.$token, 1);
            }
        }
        header('Access-Control-Allow-Origin:*');
        header('Content-type: text/json;');
        echo json_encode($view_data);
    }

    #皮肤数据
    public function skin(Request $request)
    {

    }

    #密码登录操作
    public function login(Request $request)
    {
        $mobile = $request->input('mobile');
        $password = $request->input('password');

        if ($mobile == '' || $password == '') {
            $view_data = array(
                'status' => 'false',
                'code'   => '405'
            );
            echo json_encode($view_data); exit;
        }

        $user_model = new \App\Model\User_model();
        $userinfo = $user_model->userUnique($mobile);
        if ($userinfo === FALSE) {
            $view_data = array(
                'status' => 'false',
                'code' => '404'
            );
        } elseif ($user_model->encryption($password, $userinfo->salt) != $userinfo->password) {
            $view_data = array(
                'status' => 'false',
                'code' => '400'
            );
        } else {
            $view_data = array(
                'status' => 'true',
                'code' => '0',
                'access_token' => 'in88888888'
            );
        }
        echo json_encode($view_data);
    }

    #短信快捷登录操作
    public function quicklogin(Request $request)
    {
        $mobile = $request->input('mobile');
        $smsscode = $request->input('smsscode');

        $user_model = new \App\Model\User_model();
        $userinfo = $user_model->userUnique($mobile);
        if ($userinfo === FALSE) {
            $view_data = array(
                'status' => 'false',
                'code' => '404'
            );
        } else {
            $codeInfo = $this->app_model->checkSmsscode($mobile, $smsscode, 'login');
            if ($codeInfo['result'] == 'true') {    #验证成功
                #成功之后，记录用户信息  todo:

                $view_data = array(
                    'status' => 'true',
                    'code'   => '0'
                );
            } else {
                $view_data = array(
                    'status' => 'false',
                    'code' => '400'
                );
            }
        }
        echo json_encode($view_data);
    }

    #第三方登录
    public function thirdLogin(Request $request)
    {
        $channel = $request->input('channel');
        $openid = $request->input('openid');

        #查看绑定情况，如果已经绑定原用户，就返回登录信息
        #如果没有绑定原用户，就提示绑定原系统用户或注册
        $result = 'true';
        if ($result == 'true') {
            $view_data = array(
                'status' => 'true'
            );
        } else {
            $view_data = array(
                'status' => 'false',
            );
        }
        echo json_encode($view_data);
    }

    public function captcha(Request $request)
    {
        $token = $request->input('token');
        $type = $request->input('type');

        $busy_mobile = '1233';
        if ($token == $busy_mobile) {
            $result = 'reject';
        } elseif ($token == '') {
            $result = 'true';
        } else {
            $result = 'false';
        }

        if ($result == 'true') {
            $view_data = array(
                'status' => 'true',
                'code' => '0',
                'captcha' => 'http://img.hb.aicdn.com/9a274ec4e78e37030886c89a155900d5fc4271f554e73-U54dBI_fw658'
            );
        } elseif ($result == 'reject') {
            $view_data = array(
                'status' => 'false',
                'code' => '402'       #请求过于频繁，请等待
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code' => '400'       #图形验证码不正确
            );
        }
        echo json_encode($view_data);

    }

    #获取短信验证码
    public function getSmsscode(Request $request)
    {
        $mobile = $request->input('mobile');
        $captcha = $request->input('captcha');
        $type = $request->input('type');

        if (trim($type) == '' || $mobile == '') {
            $view_data = array(
                'status' => 'false',
                'code' => '405'
            );
            echo json_encode($view_data); exit;
        }

        $code = $this->createCode(6);
        $result = $this->app_model->insertSmsscode($mobile, $code, $type);
        if ($result === TRUE) { #发送短信

            $view_data = array(
                'status' => 'true',
                'code' => '0',
                'data' => $code     #正式环境，屏蔽掉
            );
            echo json_encode($view_data); exit;
        }

        /*if ($result == 'true') {
            $view_data = array(
                'status' => 'true',
                'code' => '0'
            );
        } elseif ($result == 'reject') {
            $view_data = array(
                'status' => 'false',
                'code' => '402'       #请求过于频繁，请等待
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code' => '400'       #图形验证码不正确
            );
        }*/
        #echo json_encode($view_data);
    }

    #校验短信验证码
    public function checkSmsscode(Request $request)
    {
        $mobile = $request->input('mobile');
        $type = $request->input('type');
        $code = $request->input('code');
        if ($mobile == '' || $type == '') {
            $view_data = array(
                'status' => 'false',
                'code'   => '405'
            );
            echo json_encode($view_data); exit;
        }

        $result = $this->app_model->checkSmsscode($mobile, $code, $type);
        echo json_encode($result);
    }

    #检测手机号是否存在
    public function checkMobile(Request $request)
    {
        $mobile = $request->input('mobile');
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userUnique($mobile);
        if ($userInfo === 'false') {
            $view_data = array(
                'status' => 'false',
                'code'   => '404'
            );
        } else {
            $view_data = array(
                'status' => "true",
                'code'   => '0'
            );
        }
        echo json_encode($view_data);
    }

    #注册
    public function register(Request $request)
    {
        $mobile = $request->input('mobile');
        $smsscode = $request->input('smsscode');
        $password = $request->input('password');
        $invite_code = $request->input('invite_code');

        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userUnique($mobile);
        if ($userInfo !== FALSE) {
            $view_data = [
                'status' => 'false',
                'code' => '401'       #手机号已经注册
            ];
        } else {
            $codeInfo = $this->app_model->checkSmsscode($mobile, $smsscode, 'reg');
            if ($codeInfo['result'] == 'true') {
                $user = $user_model->createUser($mobile, $password);
                if ($user === TRUE) {   #注册成功
                    #todo 填写邀请码，使用队列做处理
                    $view_data = array(
                        'status' => 'true',
                        'code' => '0',
                        'access_token' => 'in88888888'
                    );
                } else {
                    $view_data = array(
                        'status' => 'false',
                        'code' => '400'
                    );
                }
            } else {
                $view_data = array(
                    'status' => 'false',
                    'code' => '400'       #短信验证码不正确
                );
            }
        }
        echo json_encode($view_data);
    }

    #忘记密码
    public function forgetPassword(Request $request)
    {
        $mobile = $request->input('mobile');
        $smsscode = $request->input('smsscode');
        $password = $request->input('password');

        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userUnique($mobile);
        if ($userInfo === FALSE) {
            $view_data = [
                'status' => 'false',
                'code' => '405'
            ];
            echo json_encode($view_data); exit;
        } else {
            $codeInfo = $this->app_model->checkSmsscode($mobile, $smsscode, 'forget');
            if ($codeInfo['result'] == 'true') {    #验证成功
                #成功之后,修改密码
                $back = $user_model->resetPassword($userInfo->user_id, $password, $userInfo->salt);
                if ($back == TRUE) {
                    $view_data = array(
                        'status' => 'true',
                        'code' => '0'
                    );
                } else {
                    $view_data = array(
                        'status' => 'false',
                        'code' => '400'
                    );
                }
            } else {
                $view_data = array(
                    'status' => 'false',
                    'code' => '409'
                );
            }
        }
        echo json_encode($view_data);
    }

    #服务条款
    public function service()
    {
        return view('service');
    }

    #印刷条款
    public function privacy()
    {

    }

    #帮助
    public function helper()
    {
        return view('helper');
    }

    #反馈
    public function feedback(Request $request)
    {
        if ($request->hasFile('headimg') && $request->file('headimg')->isValid()) {
            $file = $request->file('headimg');
            var_dump($file);
            $extension = $file->extension();
            $store_result = $photo->store();
            var_dump($store_result);
        }

    }

    public function uploadFile()
    {
        return view('uploadFile');
    }

    public function uploadHead(Request $request)
    {
        if ($request->hasFile('imgfile') && $request->file('imgfile')->isValid()) {
            $file = $request->file('imgfile');
            $size = $file->getSize();
            if ($size > 512500) {
                $view_data = array(
                    'status' => 'true',
                    'code'   => '505',
                    'info'   => 'too big'
                );
                echo json_encode($view_data); exit;
            }
            $extension = $file->extension();
            #$clientName = $file -> getClientOriginalName();
            #$clientPath = $file -> getRealPath();
            $newName = base64_encode(rand(10000000,999999999)).'.'.$extension;
            #$path = $file -> move('./uploads',$newName);
            $path = false;
            $realpath = 'http://api.hzz.com'.'/uploads/'.$newName;
            if ($path) {
                $view_data = array(
                    'status' => 'true',
                    'code'   => '0',
                    'data'   => $realpath
                );
            } else {
                $view_data = array(
                    'status' => 'false',
                    'code'   => '400'
                );
            }
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '400'
            );
        }
        echo json_encode($view_data);
    }

}