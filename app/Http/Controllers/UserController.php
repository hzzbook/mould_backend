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
        parent::__construct($request);
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

    #保存个人资料 todo 测试
    public function saveUserinfo(Request $request)
    {
        $headimg = $request->input('headimg');
        $nick    = $request->input('nick');
        $sex    = $request->input('sex');
        $birthday    = $request->input('birthday');
        $city    = $request->input('city');
        $introduction    = $request->input('introduction');
        $user_model = new \App\Model\User_model();
        $updateData = array(
            'headimg' => $headimg,
            'nick'    => $nick,
            'sex'    => $sex,
            'city' => $city,
            'birthday' => $birthday,
            'introduction' => $introduction,
        );
        $back = $user_model->saveUserinfo($this->userid, $updateData);
        if ($back == true) {
            $view_data = array(
                'status' => 'true',
                'code'   => '0'
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '888'
            );
        }
        echo json_encode($view_data);
    }

    #帐号绑定情况 todo 测试
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

    #更改支付密码
    public function changePayPassword(Request $request)
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
        if ($user_model->encryption($old_password, $userInfo->salt) != $userInfo->paypassword) {
            $view_data = array(
                'status' => 'false',
                'code' => '400'
            );
            echo json_encode($view_data);exit;
        }

        #重设密码
        $back = $user_model->resetPayPassword($this->userid, $new_password, $userInfo->salt);
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

    #手机更换支付密码
    public function changePayPasswordMobile(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $userInfo = $user_model->userById($this->userid);
        $password = $request->input('password');
        $back = $user_model->resetPayPassword($this->userid, $password, $userInfo->salt);
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

    #实名认证 todo  添加第三方验证
    public function realAuth(Request $request)
    {
        $fullname = $request->input('fullname');
        $idcard   = $request->input('idcard');

        #使用第三方接口验证身份信息
        $authResult = true;
        if ($authResult == true) {
            $user_model = new \App\Model\User_model();
            $back = $user_model->realAuth($this->userid, $fullname, $idcard);
            if ($back == true) {
                $view_data = array(
                    'status' => 'true',
                    'code'   => '0'
                );
            } else {
                $view_data = array(
                    'status' => "false",
                    'code' => "900"
                );
            }
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => "899"
            );
        }
        echo json_encode($view_data);
    }

    #收货地址
    public function addresses()
    {
        $user_model = new \App\Model\User_model();
        $addressInfo = $user_model->addresses($this->userid);
        echo json_encode($addressInfo);
    }

    #收货地址详情
    public function address(Request $request)
    {
        $addressId = $request->input('addressId');
        $user_model = new \App\Model\User_model();
        $addressInfo = $user_model->address($addressId, $this->userid);
        echo json_encode($addressInfo);
    }

    #添加收货地址
    public function addAddress(Request $request)
    {
        $fullname = $request->input('fullname');
        $mobile = $request->input('mobile');
        $district = $request->input('district');
        $detail = $request->input('detail');
        $is_default = $request->input('is_default');

        $user_model = new \App\Model\User_model();
        $addressData = array(
            'fullname' => $fullname,
            'mobile'  => $mobile,
            'district' => $district,
            'detail'   => $detail,
            'is_default' => $is_default
        );
        $addressInfo = $user_model->address($this->userid, $addressData);
        echo json_encode($addressInfo);
    }

    #删除收货地址
    public function deleteAddress()
    {
        $addressId = $request->input('addressId');
        $user_model = new \App\Model\User_model();
        $addressInfo = $user_model->deleteAddress($addressId, $this->userid);
        echo json_encode($addressInfo);
    }

    #编辑收货地址
    public function saveAddress(Request $request)
    {
        $fullname = $request->input('fullname');
        $mobile = $request->input('mobile');
        $district = $request->input('district');
        $detail = $request->input('detail');
        $is_default = $request->input('is_default');

        $addressId = $request->input('addressId');

        $user_model = new \App\Model\User_model();
        $addressData = array(
            'fullname' => $fullname,
            'mobile'  => $mobile,
            'district' => $district,
            'detail'   => $detail,
            'is_default' => $is_default
        );
        $addressInfo = $user_model->saveAddress($this->userid, $addressId, $addressData);
        echo json_encode($addressInfo);
    }

    #消息列表 todo
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

    #消息详情
    public function message(Request $request)
    {
        $token = $request->input('token');
        $message_id = $request->input('message_id');

        $userModel = new \App\Model\User_model();
        $messageInfo = $userModel->message($this->userid, $message_id);
        if ($messageInfo !='') {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'data'   => $messageInfo
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '404'
            );
        }
        echo json_encode($view_data);
    }

    #标记为已读 todo
    public function readedMessage(Request $request)
    {
        $message_id = $request->input('message_id');

        $userModel = new \App\Model\User_model();
        $messageInfo = $userModel->message($this->userid, $message_id);

        if ($messageInfo != '') {
            $view_data = array(
                'stauts' => 'true',
                'code' => '0',
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code' => '400',
                'info'  => '该消息已经是已读了'
            );
        }

        echo json_encode($view_data);
    }

    #删除消息 todo
    public function deleteMessage(Request $request)
    {
        $token = $request->input('token');
        $message_id = $request->input('message_id');

        $userModel = new \App\Model\User_model();
        $messageInfo = $userModel->deleteAddress($this->userid, $message_id);

        if ($messageInfo != '') {
            $view_data = array(
                'stauts' => 'true',
                'code' => '0',
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code' => '400',
                'info'  => '删除失败'
            );
        }

        echo json_encode($view_data);
    }

    #签到情况
    public function signInfo(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $signInfo = $user_model->signInfo($this->userid);
        if ($signInfo == '') {
            $view_data = array(
                'status' => 'false',
                'code'   => '866',
                'info'   => '尚无签到记录'
            );
        } else {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'data'   => $signInfo
            );
        }
        echo json_encode($view_data);
    }

    #签到操作 todo 发放积分
    public function sign(Request $request)
    {
        $user_model = new \App\Model\User_model();
        $signInfo = $user_model->sign($this->userid);
        if ($signInfo['status'] === 'true') {
            $continue = $signInfo['continue'];   #根据连续签到次数增加积分
            $sign_id = $signInfo['insert_id'];   #签到id

            #$integral = new

            $view_data = array(
                'status' => 'true',
                'code'   => '0'
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '876'       #同一天不能重复签到
            );
        }
        echo json_encode($view_data);
    }

    #反馈
    public function feedback(Request $request)
    {
        $type = $request->input('type');
        $content = $request->input('content');
        $imglist = $request->input('imglist');
        $app_model = new \App\Model\App_model();
        $result = $app_model->feedback($this->userid, $type, $content, $imglist);
        if ($result == true) {
            $view_data = array(
                'status' => 'true',
                'code'   => '0'
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '900',
                'info'   => '反馈失败'
            );
        }
        echo json_encode($view_data);
    }


    #收藏文章  todo
    public function collectArticle(Request $request)
    {
        $articleId = $request->input('articleId');

    }

}