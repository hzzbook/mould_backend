<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Cms_article;
class ContentController extends AppController
{

    public function newsList(Request $request)
    {
        $cateid = $request->input('cateid');

        $model = new \App\Model\Cms_article();
        if ($cateid == '')
            $cateid = 'no';
        $num = 4;
        $data = $model->articles($cateid, $num);

        if ($data['current_page'] > $data['total']) {
            $view_data = array(
                'status' => 'false',
                'code' => '500'       #说明没有更多的数据
            );
        } else {
            $view_data = array(
                'status' => 'true',
                'code' => '0',
            );
            $view_data = array_merge($view_data, $data);
        }
        echo json_encode($view_data);
    }

    public function newsItem(Request $request)
    {
        $id = $request->input('id');
        $model = new \App\Model\Cms_article();
        if ($id == '')
            $id = '1';

        $data = $model->article($id);

        if ($data === FALSE) {
            $view_data = array(
                'status' => 'false',
                'code' => '404'
            );
        } else {
            $view_data = array(
                'status' => 'true',
                'code' => '0',
                'data' => $data
            );
        }
        echo json_encode($view_data);
    }

    public function articles()
    {
        $model = new \App\Model\Cms_article();
        $cateid = 1;
        $num = 4;
        $data = $model->articles($cateid, $num);
        #$data = $model::where("cate_id", 1)->count();
        echo json_encode($data);
    }

    public function addArticle()
    {
        $model = new \App\Model\Cms_article();
        $data = $model->addArticle();
        var_dump($data);
    }


}