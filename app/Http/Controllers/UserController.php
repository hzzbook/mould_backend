<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

#todo 分享功能
class UserController extends AppController
{
    protected $_request;
    protected $userid;

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->_request = $request;
        $auth_info = $this->getUserByToken($this->_request->input('token'));
        if ($auth_info == FALSE) {
            $error_403 = array(
                'status' => 'false',
                'code'   => '403'
            );
            echo json_encode($error_403); exit;
        } else {
            $this->userid = $auth_info;
        }
    }

    #个人资料
    public function userinfo()
    {
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userById($this->userid);

        if ($userInfo === FALSE) {
            $view_data = array(
                'status' => 'false',
                'code'   => '400'
            );
        } else {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'data'   => $userInfo
            );
        }

        echo json_encode($view_data);
    }

    #上传头像
    public function uploadHead(Request $request)
    {
        if ($request->hasFile('headimg') && $request->file('headimg')->isValid()) {
                $file = $request->file('headimg');
                $extension = $file->extension();
                #$clientName = $file -> getClientOriginalName();
                #$clientPath = $file -> getRealPath();
                $newName = base64_encode(rand(10000000,999999999)).'.'.$extension;
                $path = $file -> move('./uploads',$newName);
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

    #保存个人资料
    public function saveUserinfo(Request $request)
    {
        $headimg = $request->input('headimg');
        $nick    = $request->input('nick');
        $view_data = array(
            'status' => 'true',
            'code'     => '0'
        );
    }

    #帐号绑定情况
    public function thirdAccount(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userById($this->userid);

        $view_data = array(
            'status' => 'true',
            'code' => '0',
            'data' => array(
                'qq' => $userInfo->qq_openid == ''?0:1,
                'wecaht' => $userInfo->wechat_openid == ''?0:1,
                'mobile' => $userInfo->mobile == ''?0:1,
            )
        );

        echo json_encode($view_data);
    }

    #更改手机号前发送短信验证
    public function changeMobileSmss(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userById($this->userid);
        $code = $this->createCode(6);

        $request = $this->app_model->insertSmsscode($userInfo->mobile, $code, 'prechg');
        #todo 发送短信
        $view_data = array(
            'status' => 'true',
            'code' => '0'
        );
        echo json_encode($view_data);
    }

    #更改手机号前验证短信验证码
    public function checkMobileSmss(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userById($this->userid);
        $smsscode = $request->input('smsscode');

        $codeInfo = $this->app_model->checkSmsscode($userInfo->mobile, $smsscode, 'prechg');
        if ($codeInfo['result'] == 'true') {
            $view_data = array(
                'status' => 'true',
                'code'   => '0'
            );
        } else {
            $view_data = array(
                'status' => 'true',
                'code'   => '400'
            );
        }
        echo json_encode($view_data);
    }

    #更改手机号
    public function changeMobile(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userById($this->userid);
        $new_mobile = $request->input('mobile');
        if ($userInfo->mobile == $new_mobile && $new_mobile != '') {
            $view_data = array(
                'status' => 'false',
                'code' => '501',
                'info' => 'The new  phone number is the same as the old phone number'
            );
            echo json_encode($view_data); exit;
        }

        $smsscode   = $request->input('smsscode');
        $codeInfo = $this->app_model->checkSmsscode($new_mobile, $smsscode, 'chgmb');
        if ($codeInfo['result'] == 'true') {
            $changeInfo = $user_model->changeMobile($this->userid, $new_mobile);
            if ($changeInfo !== FALSE) {
                $view_data = array(
                    'status' => 'true',
                    'code' => '0'
                );
            } else {
                $view_data = array(
                    'status' => 'false',
                    'code'   => '400'
                );
            }
        } else {
            $view_data = array(
                'status' => 'true',
                'code'   => '400'
            );
        }

        echo json_encode($view_data);
    }

    #更改密码
    public function changePassword(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userById($this->userid);
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');
        if ($old_password != '' && $new_password != '') {
            $view_data = array(
                'status' => 'false',
                'code'   => '405'
            );
        }

        #验证旧密码是否正确
        if ($user_model->encryption($old_password, $userInfo->salt) != $userInfo->password) {
            $view_data = array(
                'status' => 'false',
                'code' => '400'
            );
            echo json_encode($view_data);exit;
        }

        #重设密码
        $back = $user_model->resetPassword($this->userid, $new_password, $userInfo->salt);
        if ($back === TRUE) {
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
        echo json_encode($view_data);
    }

    #手机号改密码前验证短信
    public function changePasswordSmss(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userById($this->userid);
        $code = $this->createCode(6);

        $request = $this->app_model->insertSmsscode($userInfo->mobile, $code, 'prechgpw');
        #todo 发送短信
        $view_data = array(
            'status' => 'true',
            'code' => '0'
        );
        echo json_encode($view_data);
    }

    #手机号改密码前验证短信
    public function checkPasswordSmss(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userById($this->userid);
        $smsscode = $request->input('smsscode');

        $codeInfo = $this->app_model->checkSmsscode($userInfo->mobile, $smsscode, 'prechgpw');
        if ($codeInfo['result'] == 'true') {
            $view_data = array(
                'status' => 'true',
                'code'   => '0'
            );
        } else {
            $view_data = array(
                'status' => 'true',
                'code'   => '400'
            );
        }
        echo json_encode($view_data);
    }

    #手机更换密码
    public function changePasswordMobile(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userById($this->userid);
        $password = $request->input('password');
        $back = $user_model->resetPassword($this->userid, $password, $userInfo->salt);
        if ($back === TRUE) {
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
        echo json_encode($view_data);
    }

    #消息列表
    public function messages(Request $request)
    {
        $view_data = array(
            'status' => "true",
            'data'   => array(
                array('id' => 1, 'title' => '标题1', 'readed' => '0', 'cover' => 'http://img.hb.aicdn.com/d37164a8790552a9cc4e9626a5af6a659f20c4711f12b-sL48Lp_fw658', 'summary'=>'内容'),
                array('id' => 2, 'title' => '标题2', 'readed' => '0', 'cover' => 'http://img.hb.aicdn.com/d37164a8790552a9cc4e9626a5af6a659f20c4711f12b-sL48Lp_fw658', 'summary'=> '内容'),
                array('id' => 3, 'title' => '标题3', 'readed' => '1', 'cover' => 'http://img.hb.aicdn.com/d37164a8790552a9cc4e9626a5af6a659f20c4711f12b-sL48Lp_fw658', 'summary'=> '内容'),
                array('id' => 4, 'title' => '标题4', 'readed' => '1','cover' => 'http://img.hb.aicdn.com/d37164a8790552a9cc4e9626a5af6a659f20c4711f12b-sL48Lp_fw658', 'summary'=> '内容'),
                array('id' => 4, 'title' => '标题5', 'readed' => '1','cover' => 'http://img.hb.aicdn.com/d37164a8790552a9cc4e9626a5af6a659f20c4711f12b-sL48Lp_fw658', 'summary'=> '内容'),
            )
        );
        echo json_encode($view_data);
    }

    #消息
    public function message(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('message_id');
        $view_data = array(
            'status' => 'true',
            'data'  => array(
                'id' => '1',
                'title' => '标题1',
                'type' => 'text',
                'content' => '内容'
            ),
        );

        $view_data = array(
            'status' => 'false',
            'code' => "400",        #说明消息已经删除
        );

        $view_data = array(
            'status' => 'false',
            'code'   => '404'       #说明消息不存在
        );

        echo json_encode($view_data);
    }

    #标记为已读
    public function messageReaded(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('message_id');

        $view_data = array(
            'stauts' => 'true',
            'code' => '0',
        );

        $view_data = array(
            'status' => 'false',
            'code' => '400',
            'info'  => '该消息已经是已读了'
        );

        $view_data = array(
            'status' => 'false',
            'code'   => '404',
            'info'   => '消息不存在'
        );
        echo json_encode($view_data);
    }

    #删除消息
    public function messageDelete(Request $request)
    {
        $token = $request->input('token');
        $id = $request->input('message_id');

        $view_data = array(
            'stauts' => 'true',
            'code' => '0',
        );

        $view_data = array(
            'status' => 'false',
            'code' => '400',
            'info'  => '删除失败'
        );

        $view_data = array(
            'status' => 'false',
            'code'   => '404',
            'info'   => '消息不存在'
        );
        echo json_encode($view_data);
    }



}