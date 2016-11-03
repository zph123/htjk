<?php
namespace app\index\model;
use think\Model;
use think\Db;
class Below_list extends Model
{
    //添加活动
    function activity_add($data)
    {

        return Db::table('below_list')->insert($data);

    }
    //查询所有活动
    function activity_select(){

        return Db::table('below_list')->select();
    }
    //活动删除
    function activity_del($l_id){

        return Db::table('below_list')->delete($l_id);
    }

}