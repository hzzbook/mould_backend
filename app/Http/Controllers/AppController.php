<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{

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
        $view_data = array(
            'status' => "true",
            'ad_type' => 'img',  #广告类型，img图片,gif图片，movie视频
            'ad_img' => 'http://img.hb.aicdn.com/af00fa9ae20e034b92471917fdcc6045eba597a012c80-welVUf_fw658',      #广告图片地址
            'ad_url' => 'http://www.gftbank.cn',      #广告链接
            'ad_timer' => 3,     #广告倒计时
        );
        $adpost = $request->input('ad');
        if ($adpost == '1') {
            $view_data = array(
                'status' => "false",
            );
        }elseif ($adpost == 'gif') {
            $view_data = array(
                'status' => "true",
                'ad_type' => 'gif',  #广告类型，img图片,gif图片，movie视频
                'ad_img' => 'http://storage.slide.news.sina.com.cn/slidenews/77_ori/2017_39/74766_800609_366700.gif',      #广告图片地址
                'ad_url' => 'http://www.gftbank.cn',      #广告链接
                'ad_timer' => 3,     #广告倒计时
            );
        } elseif ($adpost == 'movie') {
            $view_data = array(
                'status' => "true",
                'ad_type' => 'movie',  #广告类型，img图片,gif图片，movie视频
                'ad_img' => 'https://imgcache.qq.com/tencentvideo_v1/playerv3/TPout.swf?max_age=86400&v=20161117&vid=u0531v7tzxt&auto=0',      #广告图片地址
                'ad_url' => 'http://www.gftbank.cn',      #广告链接
                'ad_timer' => 3,     #广告倒计时
            );
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
    public function homepage()
    {
        $view_data = array(
            'status' => 'true',
            'banner' => array(
                array(
                    'img' => '',
                    'banner_type' => 'url',   #banner跳转类型   url网页打开   inner 内部跳转页面
                    'uri' => ''
                ),
                array(
                    'img' => '',
                    'banner_type' => '',
                    'uri' => ''
                ),
            ),
        );
    }

    #皮肤数据
    public function skin(Request $request)
    {

    }

    #登录操作
    public function login(Request $request)
    {
        $mobile = $request->input('mobile');
        $password = $request->input('code');


    }




}