<?php
namespace app\index\controller;
use think\Session;

class User extends Common
{
    public function index()
    {
        return view('index/userCenter');
    }
    /**
     * 加载营养均衡首页
     */
    function nutrition(){
    	return view();
    }
    /**
     * 退出登录
     */
    function quit(){
        Session::set('uid',null);
        $this->redirect('index/index');
    }
}