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

        return Db::table('below_list')->order('l_id DESC')->select();
    }
    //查询单个活动
    function activity_get($l_id){

        return Db::table('below_list')->where('l_id',$l_id)->select();
    }
    //活动删除
    function activity_del($l_id){

        return Db::table('below_list')->delete($l_id);
    }
    //修改状态
    function activity_save($l_id,$dispose){

        return Db::table('below_list')->where('l_id',$l_id)->update(['l_status'=>$dispose]);
    }
    //查看报名
    function activity_activity($l_id)
    {
        return Db::table('activity')
            ->where('l_id',$l_id)
            ->select();
    }
    function activity_nowtest($l_id)
    {
        return Db::table('nowtest')
            ->field('n_id as a_id,n_name as username,n_phone as tell,uid')
            ->where('l_id',$l_id)
            ->select();
    }

}