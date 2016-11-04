<?php
namespace app\index\controller;
use think\Controller;

class Motion extends Common
{
    //首页
    public function index()
    {
        return view('index');
    }
    //
    public function motion()
    {
        return view('motion');
    }

}