<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
class Personal extends Common
{
   
    //设置密码
    public function password()
    {
      
    }
    /*退出*/
    public function quit()
    {
        Session::set('username',null);
        echo 1;    
    }

}