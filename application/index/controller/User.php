<?php
namespace app\index\controller;


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
}