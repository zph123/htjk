<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Admin_user extends Model
{
    function refer($admin_name,$pwd)
    {
        $arr =  Db::table('admin_user')->where('u_name', $admin_name)->where('u_pwd', $pwd)->find();
       return $arr;
    }
    /*密码展示*/
    function sel_one($username)
    {
        $arr=Db::table('admin_user')->where('u_name',$username)->select();
        return $arr;
    }
    /*验证原密码*/
    function sel_password($username,$pwd)
    {
        $arr=Db::table('admin_user')->where('u_name',$username)->where('u_pwd',$pwd)->select();
        return $arr;
    }
    /*修改成功重新登录*/
    function save_password($passwd,$username)
    {
        $arr=Db::table('admin_user')->where('u_name', $username)->update(['u_pwd' => $passwd]);
        return $arr;
    }

}
?>