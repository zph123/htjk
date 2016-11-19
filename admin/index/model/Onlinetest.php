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
    public function online_search($where=1,$start)
    {
        return Db::table('order')
               ->join('gl_users u ','order.u_id = u.id ')
               ->where('type',3)
               ->where($where)
               ->order("order.addtime desc")
               ->limit($start,5)
               ->select();
    }

    /*
    *统计在线测试数据的订单
    */
    public function count_order($where)
    {
        return Db::table('order')
               ->join('gl_users u ','order.u_id = u.id ')
               ->where('type',3)
               ->where($where)
               ->select();
    }

    /*
    *修改生成报告状态
    */
    public function order_update($o_id)
    {
        return Db::table('order')
               ->where('o_id',$o_id)
               ->setField('status',1);
    }


    /**
     * 数据详情查询
     */
    public function one_select($id)
    {
        return Db::table('onlinetest')->where('o_id',$id)->find();

    }

}