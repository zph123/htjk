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
       $today=86400*365;
       $u_name = Request::instance()->post('u_name');
       //判断名字是否为手机 分别查俩个数据
       $password = Request::instance()->post('password');
       if (strlen ( $u_name) != 11 || ! preg_match ( '/^1[3|4|5|7|8][0-9]\d{4,8}$/', $u_name )){
           $arr = Db::table('gl_users')
               ->where('name', $u_name)
               ->where('password', $password)
               ->find();
       }else{
           $arr = Db::table('gl_users')
               ->where('phone', $u_name)
               ->where('password', $password)
               ->find();
       }
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
