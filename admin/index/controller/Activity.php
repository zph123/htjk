<?php
namespace app\index\controller;
use think\Controller;

class Activity extends Common
{
    //首页
    public function index()
    {
        return view('index');
    }

    //活动添加页面
    public function activity_add()
    {
        return view('activity_add');
    }


}