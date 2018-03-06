<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\App_ad;
use App\Model\App_model;
use App\Model\User_model;
use App\Model\Integral_model;

#todo 分享功能
class AppController extends Controller
{

    var $app_model;

    public function __construct(Request $request)
    {
        $this->app_model = new \App\Model\App_model();
        header('Access-Control-Allow-Origin:*');
    }

    #创建Token
    public function ceateToken($stats)
    {
        $statstring = serialize($stats);
        $md5Token = md5(uniqid(md5($statstring),true));
        $md5Token_key = md5($md5Token);
        Redis::set($md5Token_key, $md5Token);
        return $md5Token;
    }

    public function uuid()
    {
        return md5(uniqid(md5(microtime(true)),true));
    }

    #验证Token
    public function checkToken($token)
    {
        if ($token == '') {
            return FALSE;
        }
        $token_key = md5($token);
        $token_value = Redis::get($token_key);
        if ($token != '' && $token == $token_value) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    #验证Token并且返回
    public function backCheckToken($token)
    {
        if ($this->checkToken($token) === FALSE) {
            $view_data = array(
                'status' => 'false',
                'code'   => '100',
                'info'   => 'Token does not exist '
            );
            echo json_encode($view_data); exit;
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

    public function ip(Request $request)
    {
        var_dump($request->getClientIp());
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

        if ($this->checkToken($token) === FALSE) {
            $token = $this->ceateToken($stat);
        }

        $model = new \App\Model\App_ad();
        $ad_data = $model->ad();
        if ($ad_data === FALSE) {
            $view_data = array(
                'status' => 'false',
                'code' => '404'
            );
        } else {
            $view_data = array(
                'status' => 'true',
                'code' => '0',
                'totken' => $token
            );
            $view_data = array_merge( $ad_data, $view_data);
        }
        echo json_encode($view_data);
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

}