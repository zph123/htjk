<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Request;
use app\index\model\Admin_user;
class Personal extends Common
{
   
    //设置密码
    public function password()
    {
        return view("password");
    }
    /*退出*/
    public function quit()
    {
        Session::set('username',null);
        echo 1;    
    }
    /*验证密码*/
    public function verify_password()
    {
        $username=Session::get('username');
        $new_code  = Request::instance()->post('new_code');
        $passwd=md5(md5($new_code));
        $model=new Admin_user();
        $arr=$model->save_password($passwd,$username);
        if($arr){
            Session::set('username',null);
            echo 1;
        }
    }
    /*旧密码验证*/
    public function one_password()
    {
        $username=Session::get('username');
        $password  = Request::instance()->post('password');
        $pwd=md5(md5($password));
        $model=new Admin_user();
        $arr=$model->sel_password($username,$pwd);
        if($arr)
        {
            echo 1;
        }else{
            echo 2;
        }
    }
    /*新密码验证*/
    public function two_password()
    {
        $new_code  = Request::instance()->post('new_code');
        $length=strlen($new_code);
        if($length<5){
            echo 2;
        }else{
            echo 1;
        }
    }
    /*确认密码验证*/
    public function three_password()
    {
        $confirm_pass = Request::instance()->post('confirm_pass');
        $new_code     = Request::instance()->post('new_code');
        if($confirm_pass==$new_code){
            echo 1;
        }else{
            echo 2;
        }
    }

}