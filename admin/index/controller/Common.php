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
       // var_dump(Session::has('username'));die;
		if(empty(Session::has('username'))){
            $this->redirect("login/login");
        }
	}

}