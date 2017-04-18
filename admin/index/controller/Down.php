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
        $data = DB::table('user_pdf')->page($page,50)->select();
        $count = DB::table('user_pdf')->count();
        $total_count=ceil($count/50);
        $this->assign('data',$data);
        $this->assign('page',$page);
        $this->assign('total_count',$total_count);
        return view('index');
    }
    public function user(){
        $data = DB::table('gl_users')->select();
        $count = count($data);
        $page = Request::instance()->get('page')?Request::instance()->get('page'):1;
        if($page<1){
            $page=1;
        }
        $data = DB::table('gl_users')->page($page,100)->select();
        $this->assign('data',$data);
        $this->assign('page',$page);
        $this->assign('count',$count);
        return view("user");
    }
}