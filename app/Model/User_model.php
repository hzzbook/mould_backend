<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User_model extends Model
{
    #盐值生成
    private function createSalt($length)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
    // 这里提供两种字符获取方式
    // 第一种是使用 substr 截取$chars中的任意一位字符；
    // 第二种是取字符数组 $chars 的任意元素
    // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    #加密
    public function encryption($password, $salt)
    {
        return md5(md5($password, $salt).'IJ#$');
    }

    #创建用户
    public function createUser($mobile, $password)
    {
        $salt = $this->createSalt(4);
        $newPassword = $this->encryption($password, $salt);
        $username = '用户'.substr($mobile, -4);
        $back = DB::table('user_users')
            ->insert([
                'username' => $username,
                'password' => $newPassword,
                'salt'  => $salt,
                'mobile' => $mobile
            ]);
        return $back;
    }

    #保存用户信息
    public function saveUserinfo($id, $data)
    {
        $back = DB::table('user_users')
            ->where('user_id', $id)
            ->update($data);
        return $back;
    }

    #重置密码
    public function resetPassword($id, $password, $salt)
    {
        $newPassword = $this->encryption($password, $salt);
        $back = DB::table('user_users')
            ->where('user_id', $id)
            ->update([
                'password' => $newPassword
            ]);
        return $back;
    }

    #重置交易密码
    public function resetPayPassword($id, $password, $salt)
    {
        $newPassword = $this->encryption($password, $salt);
        $back = DB::table('user_users')
            ->where('user_id', $id)
            ->update([
                'paypassword' => $newPassword
            ]);
        return $back;
    }

    #实名认证
    public function realAuth($id, $fullname, $idcard)
    {
        $back = DB::table('user_users')
            ->where('user_id', $id)
            ->update([
                'fullname' => $fullname,
                'idcard' => $idcard
            ]);
        return $back;
    }

    #收货地址列表
    public function addresses($userId)
    {
        $back = DB::table('user_address')
            ->where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('address_id', 'desc')
            ->get();
        return $back;
    }

    #收货地址详情
    public function address($addressId, $userId)
    {
        $back = DB::table('user_address')
            ->where('address_id',$addressId)
            ->where('user_id', $userId)
            ->get();
        return $back;
    }

    #添加收货地址
    public function addAdderss($userId, $data)
    {
        $data['user_id'] = $userId;
        $back = DB::table('user_address')
            ->insert($data);
        return $back;
    }

    #删除收货地址
    public function deleteAddress($userId, $addressId)
    {
        $back = DB::table('user_address')
            ->where('address_id', $addressId)
            ->where('user_id', $userId)
            ->delete();
        return $back;
    }

    #编辑收货地址
    public function saveAddress($userId, $addressId, $data)
    {
        $back = DB::table('user_address')
            ->where('user_id', $userId)
            ->where('address_id', $addressId)
            ->update($data);
        return $back;
    }

    #更改手机号
    public function changeMobile($id, $mobile)
    {
        $back = DB::table('user_users')
            ->where('user_id', $id)
            ->update([
                'mobile' => $mobile
            ]);
        return $back;
    }

    #判断唯一性，（手机，用户名，邮箱，身份证号）
    public function userUnique($value, $key = 'mobile')
    {
        $back = DB::table('user_users')
            ->where($key, $value)
            ->get();
        if (!empty($back)) {
            $back = $back[0];
        } else {
            $back = FALSE;
        }
        return $back;
    }

    #根据id查询用户信息
    public function userById($id)
    {
        return $this->userUnique($id, 'user_id');
    }

    #签到情况
    public function signInfo($userId)
    {
        $back = DB::table('user_sign')
            ->where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->first();

        return $back;
    }

    #签到操作
    public function sign($userId)
    {
        $date = date('Y-m-d');
        $signInfo = $this->signInfo($userId);
        $continue = 1;
        $back = 'true';
        if ($signInfo == '') {
            $insertData= [
                'user_id' => $userId,
                'continue'  => $continue,
                'date'  => date('Y-m-d'),
                'logtime' => date('Y-m-d H:i:s')
            ];
            $insert_id = DB::table('user_sign')->insertGetId($insertData);
        } else {
            $chazhi = strtotime($date) - strtotime($signInfo->date);
            if ($chazhi < 3600*24) {
                $back = false;  #同一天不能重复签到
            }elseif ($chazhi >= 3600*24 && $chazhi < 7200*24) { #相差一天签到（连续签到））
                $continue = $signInfo->continue + 1;
                $insertData= [
                    'user_id' => $userId,
                    'continue'  => $continue,
                    'date'  => date('Y-m-d'),
                    'logtime' => date('Y-m-d H:i:s')
                ];
                $insert_id = DB::table('user_sign')->insertGetId($insertData);
            } else {
                $insertData=[
                    'user_id' => $userId,
                    'continue'  => $continue,
                    'date'  => date('Y-m-d'),
                    'logtime' => date('Y-m-d H:i:s')
                ];
                $insert_id = DB::table('user_sign')->insertGetId($insertData);
            }
        }
        #return $back;
        if ($back == false) {
            $result = array(
                'status' => 'false'
            );
        } else {

            $result = array(
                'status' => 'true',
                'continue' => $continue,
                'insert_id' => $insert_id
            );
        }
        return $result;
    }

    #消息列表 todo
    public function messages($userId)
    {
        $back = DB::table('user_message')
            ->where('user_id', $userId)
            ->orderBy('message_id', 'desc')
            ->paginate(5);
        return $back;
    }

    #消息详情
    public function message($userId, $messageId)
    {
        $back = DB::table('user_message')
            ->where('message_id', $messageId)
            ->where('user_id', $userId)
            ->get();
        return $back;
    }

    #删除消息
    public function deleteMessage($userId, $messageId)
    {
        $back = DB::table('user_message')
            ->where('user_id', $userId)
            ->where('message_id', $messageId)
            ->delete();
        return $back;
    }

    #设为已读
    public function readedMessage($userId, $messageId)
    {
        $back = DB::table('user_message')
            ->where('user_id', $userId)
            ->where('message_id', $messageId)
            ->update([
                'status' => 0
            ])
        return $back;
    }

}