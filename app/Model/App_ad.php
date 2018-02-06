<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class App_ad extends Model
{
    protected $table = 'app_ad';

    public function ads()
    {

    }

    public function ad()
    {
        $time = date('Y-m-d H:i:s');
        $back = $this
            ->where('starttime','<', $time)
            ->where('endtime','>', $time)
            ->get()
            ->first();
        if (!empty($back)) {
            $back = $back->toArray();
        } else {
            $back = FALSE;
        }
        return $back;
    }

}
