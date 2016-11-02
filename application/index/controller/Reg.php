<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Gl_users;

class Reg extends Common
{
	//跳转到注册页面
	public function index(){
		return view('login/reg');
	}
	//添加账号
	public function add(Request $request){
		$data = $_POST;
		$user = new Gl_users();
    	$data = $user->add_one($data);
    	return view('login/login');
	}
	//验证姓名唯一
	public function check_only(Request $request){
		$name = $_POST;
		$user = new Gl_users();
		$stauts = $user->check_one($name);
		if($stauts){
			echo 1;
		}else{
			echo 0;
		}
	}
}