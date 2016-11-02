<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Cookie;

class Login extends Controller
{
	//跳转到登录页面
	public function index(){
		return view('login');
	}
  //执行登录
    public function login(){
           $u_name = Request::instance()->post('u_name');
           $checkbox_mini = Request::instance()->post('checkbox_mini');
           $password = md5(Request::instance()->post('password'));
           $arr = Db::table('gl_users')->where('name', $u_name)->where('password', $password)->find();
           if (!empty($arr)) {
               if (!empty($checkbox_mini)) {
//                   //setcookie('uid', md5($arr['id']), time() + 3600 * 24);
                   Cookie::set('uid', md5($arr['id']), 3600 * 24);
                   Session::set('uid', $arr['id']);
               } else {
                   Session::set('uid', $arr['id']);
             }
                   echo 1;
               } else {
                   echo 2;
               }

    }
    //登录后跳转页面
    public function add(){
        echo "登录成功";
     //  echo Cookie::get('uid');
    }
    public function onlogin(){
        echo 10;
    }
}