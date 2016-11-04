<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Cookie;
use think\Session;

class Common extends Controller
{
	function _initialize(){
        $controller=request()->controller();            //控制器
        $action=request()->action();                    //方法
        Cookie::set('controller', $controller);
        Cookie::set('action', $action);
//        var_dump(Session::get('uid'));die;
		if(!Session::has('uid')){
            $this->redirect("login/index");
        }
	}

}
