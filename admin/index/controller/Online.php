<?php
namespace app\index\controller;
use think\Controller;

class Online extends Controller
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

}