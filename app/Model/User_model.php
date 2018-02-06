<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User_model extends Model
{
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

    public function encryption($password, $salt)
    {
        return md5(md5($password, $salt).'IJ#$');
    }

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

    public function changeMobile($id, $mobile)
    {
        $back = DB::table('user_users')
            ->where('user_id', $id)
            ->update([
                'mobile' => $mobile
            ]);
        return $back;
    }

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

    public function userById($id)
    {
        return $this->userUnique($id, 'user_id');
    }

}