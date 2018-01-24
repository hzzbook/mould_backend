<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redis;


#todo 分享功能
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
                    'img' => 'http://img.hb.aicdn.com/e9a5ce03594f3024f02b43d94e97ab525517bed563471-0C7vRF_fw658',
                    'banner_type' => 'url',   #banner跳转类型   url网页打开   inner 内部跳转页面
                    'uri' => 'http://www.gftbank.cn'
                ),
                array(
                    'img' => 'http://img.hb.aicdn.com/a76ab27987cd86d656085e4f7950beb8b2a1b4c8b6866-PvVIIq_fw658',
                    'banner_type' => 'inner',
                    'uri' => 'user/info'
                ),
            ),
            'new_skin' => 'false',   #是否有新皮肤
        );

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

        $test_mobile = '15988888888';
        $test_password = '12345678';

        $kong_mobile = '15900000000';
        if ($mobile == $test_mobile && $password == $test_password) {
            $result = 'true';
        } elseif ($mobile == $kong_mobile) {
            $result = 'no';
        } else {
            $result = 'false';
        }

        if ($result == 'true') {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'access_token' => 'in88888888'
            );
        } elseif ($result == 'no') {
            $view_data = array(
                'status' => 'false',
                'code'   => '404'
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '400'
            );
        }
        echo json_encode($view_data);
    }

    #短信快捷登录操作
    public function quicklogin(Request $request)
    {
        $mobile = $request->input('mobile');
        $smsscode = $request->input('smsscode');

        $test_mobile = '15988888888';
        $test_code = '333333';

        if ($mobile == $test_mobile && $smsscode == $test_code) {
            $result = 'true';
        } elseif ($mobile == $test_mobile && $smsscode != $test_code) {
            $result = 'no';
        } else {
            $result = 'false';
        }

        if ($result == 'true') {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'access_token' => 'in88888888'
            );
        } elseif ($result == 'no') {
            $view_data = array(
                'status' => 'false',
                'code'   => '400'       #短信验证码不正确
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '400'
            );
        }
        echo json_encode($view_data);
    }

    #第三方登录
    public function thirdLogin(Request $request)
    {
        $channel = $request->input('channel');
        $openid  = $request->input('openid');

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
        $type  = $request->input('type');

        $busy_mobile = '1233';
        if ($token == $busy_mobile) {
            $result = 'reject';
        } elseif ($token =='') {
            $result = 'true';
        } else {
            $result = 'false';
        }

        if ($result == 'true') {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'captcha' => 'http://img.hb.aicdn.com/9a274ec4e78e37030886c89a155900d5fc4271f554e73-U54dBI_fw658'
            );
        } elseif ($result == 'reject') {
            $view_data = array(
                'status' => 'false',
                'code'   => '402'       #请求过于频繁，请等待
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '400'       #图形验证码不正确
            );
        }
        echo json_encode($view_data);

    }

    public function getSmsscode(Request $request)
    {
        $mobile = $request->input('mobile');
        $captcha = $request->input('captcha');
        $type  = $request->input('type');

        $test_mobile = '15988888888';
        $test_captcha = 'ased';
        $busy_mobile = '15966666666';
        if ($mobile == $test_mobile &&  $test_captcha == $captcha) {
            $result = 'true';
        }elseif ($mobile == $test_mobile && $test_captcha != $captcha) {
            $result = 'false';
        } elseif ($mobile == $busy_mobile) {
            $result = 'reject';
        } else {
            $result = 'false';
        }

        if ($result == 'true') {
            $view_data = array(
                'status' => 'true',
                'code'   => '0'
            );
        } elseif ($result == 'reject') {
            $view_data = array(
                'status' => 'false',
                'code'   => '402'       #请求过于频繁，请等待
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '400'       #图形验证码不正确
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

        $test_mobile = '15988888888';
        $test_password = '12345678';
        $test_code = '333333';

        if ($mobile == $test_mobile && $smsscode == $test_code) {
            $result = 'true';
        } elseif ($mobile == $test_mobile && $smsscode != $test_code) {
            $result = 'no';
        } else {
            $result = 'false';
        }

        if ($result == 'true') {
            $view_data = array(
                'status' => 'true',
                'code'   => '0',
                'access_token' => 'in88888888'
            );
        } elseif ($result == 'no') {
            $view_data = array(
                'status' => 'false',
                'code'   => '400'       #短信验证码不正确
            );
        } else {
            $view_data = array(
                'status' => 'false',
                'code'   => '400'
            );
        }
        echo json_encode($view_data);
    }

    #上拉加载更多
    public function pullup(Request $request)
    {
        $cate = $request->input('category');
        $view_data = array(
            array(
                'id' => '1',
                'title' => '测试数据1', #标题
                'cover' => 'http://img.hb.aicdn.com/c9196acdb337c8d74e70b654bc4d9b0ee69b4c5820ee2-CGdpi9_fw658',  #封面
                'description' => '描述',  #描述
                'cateid' => '1',
                'category' => '分类1',
                'ishot'  => '1',
                'pv' => '0',
                'praise' => '0',
                'logtime'   => '1516068637'
            ),
            array(
                'id' => '2',
                'title' => '测试数据2', #标题
                'cover' => 'http://img.hb.aicdn.com/c9196acdb337c8d74e70b654bc4d9b0ee69b4c5820ee2-CGdpi9_fw658',  #封面
                'description' => '描述',  #描述
                'cateid' => '1',
                'category' => '分类1',
                'ishot'  => '1',
                'pv' => '0',
                'praise' => '0',
                'logtime'   => '1516068637'
            ),
            array(
                'id' => '3',
                'title' => '测试数据3', #标题
                'cover' => 'http://img.hb.aicdn.com/c9196acdb337c8d74e70b654bc4d9b0ee69b4c5820ee2-CGdpi9_fw658',  #封面
                'description' => '描述',  #描述
                'cateid' => '1',
                'category' => '分类1',
                'ishot'  => '1',
                'pv' => '0',
                'praise' => '0',
                'logtime'   => '1516068637'
            ),
            array(
                'id' => '4',
                'title' => '测试数据4', #标题
                'cover' => 'http://img.hb.aicdn.com/c9196acdb337c8d74e70b654bc4d9b0ee69b4c5820ee2-CGdpi9_fw658',  #封面
                'description' => '描述',  #描述
                'cateid' => '1',
                'category' => '分类1',
                'ishot'  => '1',
                'pv' => '0',
                'praise' => '0',
                'logtime'   => '1516068637'
            ),
            array(
                'id' => '5',
                'title' => '测试数据5', #标题
                'cover' => 'http://img.hb.aicdn.com/c9196acdb337c8d74e70b654bc4d9b0ee69b4c5820ee2-CGdpi9_fw658',  #封面
                'description' => '描述',  #描述
                'cateid' => '2',
                'category' => '分类2',
                'ishot'  => '1',
                'pv' => '0',
                'praise' => '0',
                'logtime'   => '1516068637'
            ),
        );
        echo json_encode($view_data);
    }

    #下拉刷新
    public function pulldown(Request $request)
    {
        $cate = $request->input('category');
        $page = $request->input('page');
        $num = 5;
        $start = ($page - 1) * $num;
        if ($page < 5) {
            $view_data = array(
                'status' => 'true'
            );
            for ($i = 1; $i <= 5; $i++) {
                $id = $start + 1;
                $view_data['data'][] = array(
                    array(
                        'id' => $id,
                        'title' => '测试数据' . $id, #标题
                        'cover' => 'http://img.hb.aicdn.com/c9196acdb337c8d74e70b654bc4d9b0ee69b4c5820ee2-CGdpi9_fw658',  #封面
                        'description' => '描述',  #描述
                        'cateid' => '1',
                        'category' => '分类1',
                        'ishot' => '1',
                        'pv' => '0',
                        'praise' => '0',
                        'logtime' => '1516068637'
                    ),
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
        $feedback = $request->input('feedback');

    }

    public function dbselect()
    {
        /*$users = DB::select('select * from m_user_users');
        var_dump($users);*/

        Redis::set('good', 'testgood');
        if (Redis::exists('good')) {
            $good = Redis::get('good');
            var_dump($good);
        } else {
            echo "errro";
        }
    }

}