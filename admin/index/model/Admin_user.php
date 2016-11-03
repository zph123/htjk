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

}
?>