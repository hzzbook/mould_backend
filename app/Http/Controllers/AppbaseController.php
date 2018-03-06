<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\App_ad;
use App\Model\App_model;
use App\Model\User_model;

#todo 分享功能
class AppbaseController extends AppController
{
    var $app_model;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $token = $request->input('token');
        /*if ($token != '') {
            $this->backCheckToken($token);
        }*/
    }

    #生成短信验证码
    public function createCode($length)
    {
        return mt_rand(100000,999999);
    }

    /**
     * todo
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
        $token = $request->input('token');

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
            //$token = $token;
            Redis::set($token, $userinfo->user_id);
            $view_data = array(
                'status' => 'true',
                'code' => '0'
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

    #第三方登录 todo
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

    #验证码 todo
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



    #测试上传图片
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
            $path = $file -> move('./uploads',$newName);
            #$path = false;
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

    #检测最新版本
    public function latestVersion(Request $request)
    {
        $platform = $request->input('platform');
        $version = $request->input('version');

        $appModel = new App_model();
        $versionInfo = $appModel->latestVesion($platform);

        if ($versionInfo->version_no == $version) {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'is_latest' => '1'
            );
        } else {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'is_latest' => '0',
                'latest_data'   => $versionInfo
            );
        }
        echo json_encode($view_data);
    }

    #系统公告列表
    public function affiches()
    {
        $affiche_data = $this->app_model->affiches();

        if (!empty($affiche_data['data'])) {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'data'   => $affiche_data['data']
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '404'
            );
        }
        echo json_encode($view_data);

    }

    #系统公告详情
    public function affiche(Request $request)
    {
        $id = $request->input('id');
        $affiche_info = $this->app_model->affiche($id);
        if ($affiche_info != '') {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'data'   => $affiche_info
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '404'
            );
        }
        echo json_encode($view_data);
    }



}