<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class App_model extends Model
{
    public function banners()
    {
        $banner = DB::table('app_banner')
            ->where('status', '1')
            ->orderBy('order_no', 'asc')
            ->get();
        return $banner;
    }

    public function activity()
    {
        $time = date('Y-m-d H:i:s');
        $back = DB::table('app_activity')
            ->where('starttime','<', $time)
            ->where('endtime','>', $time)
            ->get();
        #var_dump($back);
        if (!empty($back)) {
            $back = $back[0];
        } else {
            $back = FALSE;
        }
        return $back;
    }

    public function insertSmsscode($mobile, $code, $type)
    {
        $time = time();
        $back = DB::table('app_smsscode')
            ->insert([
                'mobile' => $mobile,
                'code'   => $code,
                'type'   => $type,
                'time'   => $time
            ]);
        return $back;
    }

    public function checkSmsscode($mobile, $code, $type)
    {
        $time = time()-3600*24;
        $back = DB::table('app_smsscode')
            ->where('mobile', $mobile)
            ->where('type', $type)
            ->where('code', $code)
            ->where('time', '>', $time)
            ->get();
        if (!empty($back)) {
            $info = $back[0];
            $time = time();
            $chazhi = ($time - $info->time)/60;
            if ($chazhi > 5) {  #超过三分钟，验证码失效
                $back = array(
                    'status' => 'true',
                    'code'   => '0',
                    'result' => 'false'
                );
            } else {
                $back = array(
                    'status' => 'true',
                    'code'   => '0',
                    'result' => 'true'
                );
            }
        } else {
            $back = array(
                'status' => 'true',
                'code'   => '0',
                'result' => 'false'
            );
        }
        return $back;
    }

    public function latestVesion($platform)
    {
        $back = DB::table('app_version')
            ->where('platform',$platform)
            ->orderBy('version_id', 'desc')
            ->first();
        return $back;
    }

    public function affiches()
    {
        $affiches = DB::table('app_affiche')
            ->where('status', '1')
            ->orderBy('affiche_id', 'desc')
            ->paginate(10)->toArray();
        return $affiches;
    }

    public function affiche($id)
    {
        $affiche = DB::table('app_affiche')
            ->where('affiche_id', $id)
            ->get()->first();
        return $affiche;
    }

    #提交反馈
    public function feedback($userId, $type, $content, $images)
    {
        $date = date('Y-m-d H:i:s');
        $back = DB::table('app_feedback')
            ->insert([
                'user_id' => $userId,
                'content'   => $content,
                'type'   => $type,
                'images' => $images,
                'date'   => $date
            ]);
        return $back;
    }

    public function feedbacks()
    {

    }

}