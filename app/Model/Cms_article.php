<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cms_article extends Model
{
    protected $table = 'cms_article';

    public function articles($cateid, $num)
    {
        if ($cateid != 'no') {
            $set = $this->where('cate_id', $cateid);
        } else {
            $set = $this;
        }
        $set = $set->orderBy('article_id', 'desc');
        return $set->paginate($num)->toArray();
    }

    public function article($actid)
    {
        $back = $this->where('article_id', $actid)->get()->first();
        if (!empty($back)) {
            $back = $back->toArray();
        } else {
            $back = FALSE;
        }
        return $back;
    }

    public function addArticle()
    {
        $datetime = date('Y-m-d H:i:s');
        return DB::insert('insert into m_cms_article (title, sumary, cover, url, cate_id, logtime) values (?, ?, ? , ? , ?, ? )',
            [
                '火爆朋友圈的众筹民宿，为什么我不看好它？',
                '看似前景广阔的民宿众筹，其实不怎么靠谱。',
                'https://pic.36krcnd.com/201802/01062407/ywd9qk0j2hnl71r4!heading',
                'http://36kr.com/p/5117356.html',
                '1',
                $datetime
            ]
        );
    }
}
