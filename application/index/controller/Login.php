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
       $today=86400*7;
       $u_name = Request::instance()->post('u_name');
       $password = Request::instance()->post('password');

       $arr = Db::table('gl_users')
           ->where('name', $u_name)
           ->where('password', $password)
           ->find();
       if (!empty($arr)) {
          Cookie::set('uid',$arr['id'],$today);
          echo 1;
       } else {
          echo 2;
       }
    }
    //登录后跳转页面
    public function add(){
        $controller=Cookie::get('controller');
        $action=Cookie::get('action');
        $this->redirect($controller.'/'.$action);
    }

}
