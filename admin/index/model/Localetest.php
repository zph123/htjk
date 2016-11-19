<?php
namespace app\index\model;
use think\Model;
use think\Db;
class Localetest extends Model
{

    /**
     *现场数据查询
     */
    public function online_search($where=1,$start)
    {
        return Db::table('order')
               ->alias('o')
               ->join('gl_users u ','o.u_id = u.id ')
               ->join('user_infos us ','us.u_id = o.u_id ')
               ->where('type',4)
               ->where($where)
               ->order("o.addtime desc")
               ->field('o.o_id,o.out_trade_no,o.status,o.addtime,o.is_pay,u.name,us.id_number')
               ->limit($start,5)
               ->select();
    }

    /*
    *统计现场数据的订单
    */
    public function count_order($where)
    {
        return Db::table('order')
               ->alias('o')
               ->join('gl_users u ','o.u_id = u.id ')
               ->join('user_infos us ','us.u_id = o.u_id ')
               ->where('type',4)
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
        return Db::table('nowtest')
               ->where('o_id',$id)
               ->find();

    }


}