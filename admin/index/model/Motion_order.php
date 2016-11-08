<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Motion_order extends Model
{
    //查看全部订单
    public function getAll($where)
    {
        $data= Db::table('order')
            ->alias('o')
            ->join('motion_order m ','o.o_id = m.order_id ')
            ->join('gl_users g ','o.u_id = g.id ')
            ->where('type',2)
            ->where($where)
            ->select();
        return $data;
    }
    //运动订单查询
    public function showAll($where,$start,$paging)
    {
        return Db::table('order')
            ->alias('o')
            ->join('motion_order m ','o.o_id = m.order_id ')
            ->join('gl_users g ','o.u_id = g.id ')
            ->where('type',2)
            ->where($where)
            ->limit($start,$paging)
            ->select();
    }
    //分页查询订单
    public function pageAll($offset,$rows)
    {
        $data= Db::table('motion_order')
            ->alias('m')
            ->join('gl_users g ','m.u_id = g.id ')
            ->limit($offset,$rows)
            ->select();
        return $data;
    }
    //查看单个订单
    public function getOne($u_id,$order_id)
    {
        $data= Db::table('order')
            ->alias('o')
            ->field('out_trade_no,name,addtime,is_pay,status')
            ->join('gl_users g ','o.u_id = g.id ')
            ->where('type',2)
            ->where('u_id',$u_id)
            ->where('o_id',$order_id)
            ->select();
        return $data;
    }
    //查看详情
    public function details($u_id,$begin_time,$end_time)
    {
        $data = Db::table("user_motion")
            ->where('uid',$u_id)
            ->where('time','between',[$begin_time,$end_time])
            ->order('time asc')
            ->select();
        return $data;
    }
}