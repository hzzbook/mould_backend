<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;


#todo 分享功能
class UserController extends AppController
{
    protected $_request;

    public function __construct(Request $request)
    {
        $this->_request = $request;
        $auth_info = $this->auth();
        if ($auth_info == FALSE) {
            $error_403 = array(
                'status' => 'false',
                'code'   => '403'
            );
            echo json_encode($error_403); exit;
        }
    }

    public function auth()
    {
        $access_token = $this->_request->input('access_token');
        if ($access_token == 'in88888888') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    #个人资料
    public function userinfo()
    {
        //$token = $this->request->input('access_token');
        $view_data = array(
            'status' => 'false',
            'data' => array(
                'nick' => '昵称',
                'headimg' => 'http://img.hb.aicdn.com/a0bfea33d885a131f4c9e2e95ef6e2dc431a2fe827da-JixhDx_fw658',    #头像
                'sex'    => '1',    #性别
            ),
        );
        echo json_encode($view_data);
    }

    #上传头像
    public function uploadHead(Request $request)
    {
        $file = $request->file('image');

        /*//Display File Name
        echo 'File Name: '.$file->getClientOriginalName();
        echo '<br>';

        //Display File Extension
        echo 'File Extension: '.$file->getClientOriginalExtension();
        echo '<br>';

        //Display File Real Path
        echo 'File Real Path: '.$file->getRealPath();
        echo '<br>';

        //Display File Size
        echo 'File Size: '.$file->getSize();
        echo '<br>';

        echo 'File Mime Type: '.$file->getMimeType();*/

        $destinationPath = 'uploads';
        $file->move($destinationPath,$file->getClientOriginalName());
    }

    #保存个人资料
    public function saveUserinfo(Request $request)
    {

    }

    #帐号绑定情况
    public function thirdAccount(Request $request)
    {
        $token = $request->input('token');

        $view_data = array(
            'status' => 'true',
            'qq' => '0',
            'wechat' => '0',
            'weibo' => '1'
        );

        echo json_encode($view_data);
    }

    #发送短信
    public function sendSmss(Request $request)
    {
        $token = $request->input('token');
        $mobile = $request->input('mobile');


    }

    #更改手机号
    public function changeMobile(Request $request)
    {
        $token  = $request->input('token');
        $mobile = $request->input('mobile');
        $code   = $request->input('code');

        $view_data = array(
            'status' => 'true',
            'code'   => '0',
            'info'   => 'success'
        );

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