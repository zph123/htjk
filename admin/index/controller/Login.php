<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Cookie;
use app\index\model\Admin_user;
class Login extends Controller
{
    /*登陆页面*/
    public function login()
    {
      // print_r(Session::has('username'));die;
       return view('login');
    }
    /*验证*/
    public function verify()
    {
     $admin_name = Request::instance()->post('name');

     $admin_pwd = Request::instance()->post('pwd');
     $pwd=md5(md5($admin_pwd));
       $modle=new Admin_user();
        $arr= $modle->refer($admin_name,$pwd);

     // var_dump($arr);die;
     if (!empty($arr)) 
      {
         Session::set('username', $arr['u_name']);
         echo 1;
      }else{
          echo 2;
      }
    }



}