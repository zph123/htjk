<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use app\index\model\Motion_order;

class Motion extends Common
{
    //首页
    public function index()
    {
        $model=new Motion_order();
        $data=$model->getAll();
        $this->assign('data',$data);
        return view('index');
    }
    //查看详情
    public function motiondetails()
    {
        $u_id=Request::instance()->param('u_id','','strip_tags,strtolower');
        $order_id=Request::instance()->param('order_id','','strip_tags,strtolower');
        $begin_time=Request::instance()->param('begin_time','','strip_tags,strtolower');
        $end_time=Request::instance()->param('end_time','','strip_tags,strtolower');
        $model=new Motion_order();
        $data=$model->details($u_id,$begin_time,$end_time);
        $this->assign('data',$data);
        $one=$model->getOne($u_id,$order_id);
        $this->assign('one',$one);
        return view('motiondetails');
    }
    //生成运动处方
    public function motion()
    {
        return view('motion');
    }

}