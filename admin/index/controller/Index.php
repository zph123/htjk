<?php
namespace app\index\controller;
use think\Controller;

class Index extends Controller
{
	public function _initialize()
    {
        //初始化，以后设计用
    }
    public function index()
    {
        return view('index');
    }

    public function nowTest()
    {
        return view('nowTest');
    }
    public function onlineTest()
    {
        return view('onlineTest');
    }
    public function sportAction(){
    	return view('sportAction');
    }
    public function nutrientBalance(){
    	return view('nutrientBalance');
    }
    public function predictedHeight(){
    	//暂无页面，临时跳转到index
    	return view('index');
    }
    public function userCenter(){
    	return view('userCenter');
    }
    

}
