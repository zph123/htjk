<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
class Down extends Common
{
    //首页
    public function index()
    {
        $page = Request::instance()->get('page')?Request::instance()->get('page'):1;
        if($page<1){
            $page=1;
        }
        $data = DB::table('user_pdf')->page($page,10)->select();
        $this->assign('data',$data);
        $this->assign('page',$page);
        return view('index');
    }
}