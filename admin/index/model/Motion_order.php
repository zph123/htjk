<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Motion_order extends Model
{
    //查看全部订单
    public function alls()
    {
        $data= Db::table('motion_order')
            ->alias('m')
            ->join('gl_users g ','m.u_id = g.id ')
            ->select();
        return $data;
    }
    //分页查询订单
    public function pageAll($where,$start,$paging,$name,$m_number,$is_pay,$state)
    {
        $data= Db::table('motion_order')
            ->alias('m')
            ->join('gl_users g ','m.u_id = g.id ')
            ->where($where)
            ->limit($start,$paging)
            ->select();
        return $data;
    }
    //查看单个订单
    public function getOne($u_id,$m_id)
    {
        $data= Db::table('motion_order')
            ->alias('m')
            ->field('m_number,name,add_time,is_pay,state')
            ->join('gl_users g ','m.u_id = g.id ')
            ->where('u_id',$u_id)
            ->where('m_id',$m_id)
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
            ->group('time')
            ->select();
        return $data;
    }
}