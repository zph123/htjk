<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Down extends Common
{
    //首页
    public function index()
    {
        $data = DB::table('user_pdf')->select();
        $this->assign('data',$data);
        return view('index');
    }
}