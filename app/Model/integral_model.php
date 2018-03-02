<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Integral_model extends Model
{
    protected $table = 'user_integral';

    public function info($userId)
    {
        $integralInfo = $this->where('user_id', $userId)->get()->first();
        if (!empty($integralInfo)) {
            $back = $integralInfo->toArray();
        } else {
            $back = FALSE;
        }
        return $back;
    }

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

        }
    }

    public function decrease($userId, $amout, $reason, $summary)
    {
        $alteration = '-1';
        $info = $this->info($userId);
        if ($info == false) {

        } else {
            $total = $info->total - $amout;
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

        }
    }


}