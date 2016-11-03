<?php
namespace app\index\controller;
use think\Controller;

class Activity extends Controller
{
    public function _initialize()
    {
        //初始化，以后设计用
    }
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