<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Integral_model extends Model
{
    protected $table = 'user_integral';

    #积分信息
    public function info($userId)
    {
        $integralInfo = $this->where('user_id', $userId)
            ->orderby('logtime', 'desc')
            ->get()->first();
        if (!empty($integralInfo)) {
            $back = $integralInfo->toArray();
        } else {
            $back = FALSE;
        }
        return $back;
    }

    #积分收入
    public function increase($userId, $amout, $reason, $summary)
    {
        $alteration = '1';
        $info = $this->info($userId);
        if ($info == false) {
            $total = $amout;
        } else {
            $total = $amout + $info->total;
        }
        $insertData = array(
            'user_id' => $userId,
            'amout'   => $amout,
            'time'    => date('Y-m-d H:i:s'),
            'total'   => $total,
            'reason'  => $reason,
            'sumarray' => $summary
        );
        $insert_id = $this->insertGetId($insertData);
        if ($insert_id != false) {
            $back = array(
                'status' => 'true',
                'code'   => '0'
            );
            return $back;
        }
    }

    #积分支出
    public function decrease($userId, $amout, $reason, $summary)
    {
        $alteration = '-1';
        $info = $this->info($userId);
        if ($info == false) {
            $back = array(
                'status' => 'false',
                'code'   => '788',
                'info'   => '余额不足'
            );
            return $back;
        } else {
            $total = $info->total - $amout;
            if ($total < 0) {
                $back = array(
                    'status' => 'false',
                    'code'   => '788',
                    'info'   => '余额不足'
                );
                return $back;
            }
        }
        $insertData = array(
            'user_id' => $userId,
            'amout'   => $amout,
            'time'    => date('Y-m-d H:i:s'),
            'total'   => $total,
            'reason'  => $reason,
            'sumarray' => $summary
        );
        $insert_id = $this->insertGetId($insertData);
        if ($insert_id != false) {
            $back = array(
                'status' => 'true',
                'code'   => '0'
            );
            return $back;
        }
    }

    #积分流水 todo 测试
    public function bill($userId, $starttime, $endtime)
    {
        $back = $this->where('user_id', $userId)
            ->where('logtime','>', $starttime)
            ->where('logtime','<', $endtime)
            ->paginate(10);
        return $back;
    }

}