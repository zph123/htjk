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
        $out_trade_no = Request::instance()->get('out_trade_no');
        $is_pay=Request::instance()->get('is_pay');
        $status=Request::instance()->get('status');
        $page=Request::instance()->get('page');
        $parameter=array();
        $where=array();
        if(!empty($out_trade_no)){
            $where['out_trade_no']=$out_trade_no;
        }
        if(isset($is_pay) && $is_pay!=="" ){
            $where['is_pay']=$is_pay;
        }
        if(isset($status) && $status!==""){
            $where['status']=$status;
        }
        $model = new Motion_order();
        $arr = $model->getAll($where);
        $number=count($arr);
        $paging=2;
        $leaf=ceil($number/$paging);
        $page=isset($_GET['page'])?$_GET['page']:1;
        $start=($page-1)*$paging;
        $lastpage=$page-1<1?1:$page-1;
        $nextpage=$page+1>$leaf?$leaf:$page+1;
        $data = $model->showAll($where,$start,$paging);

        $parameter['page']    =$page;
        $parameter['nextpage']=$nextpage;
        $parameter['lastpage']=$lastpage;
        $parameter['leaf']    = $leaf;
        if(!empty($out_trade_no)){
            $parameter['out_trade_no']=$out_trade_no;
        }
        if(isset($is_pay) && $is_pay!=="" ){
            $parameter['is_pay']=$is_pay;
        }
        if(isset($status) && $status!==""){
            $parameter['status']=$status;
        }

        $this->assign('page',$parameter);
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