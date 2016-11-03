<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Cookie;
class Common extends Controller
{
	function _initialize(){
		$controller=request()->controller();            //控制器
        $action=request()->action();                    //方法
        Cookie::set('controller', $controller);
        Cookie::set('action', $action);
       // var_dump(Session::has('username'));die;
		if(empty(Session::has('username'))){
            $this->redirect("login/login");
        }
	}

}