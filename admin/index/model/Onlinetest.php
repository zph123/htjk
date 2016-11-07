<?php
namespace app\index\model;
use think\Model;
use think\Db;
use \think\db\Query;
class Onlinetest extends Model
{

    /**
     * 在线测试数据查询
     */
    public function on_select($where,$start)
    {

        return Db::table('onlinetest')->where($where)->where('status',1)->limit($start,5)->select();
    }

    public function two_select($where)
    {

        return Db::table('onlinetest')->where($where)->where('status',1)->select();
    }

    public function tr_select($where)
    {

        return Db::table('onlinetest')->where($where)->where('status',2)->select();
    }

    /**
     * 数据详情查询
     */
    public function one_select($id)
    {
        return Db::table('onlinetest')->where('register_id',$id)->find();

    }

    /**
     * 在线测试报告
     */
    public function onl_test($where,$start)
    {

        return Db::table('onlinetest')->where($where)->where('status',2)->limit($start,5)->select();
    }

    /*
    *修改测试报告状态为已生成
    */
    public function updates($reg_id)
    {
        return Db::table('onlinetest')->where('register_id',$reg_id)->setField('status',2);
    }
}