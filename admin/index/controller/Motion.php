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
        $name = Request::instance()->get('name');
        $out_trade_no = Request::instance()->get('out_trade_no');
        $is_pay=Request::instance()->get('is_pay');
        $status=Request::instance()->get('status');
        $page=Request::instance()->get('page');
        $parameter=array();
        $where=array();
        if(!empty($out_trade_no)){
            $where['out_trade_no']=$out_trade_no;
        }
        if(!empty($name)){
            $where['name']=$name;
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
        $paging=5;
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
        }else{
            $parameter['out_trade_no']='';
        }
        if(!empty($name)){
            $parameter['name']=$name;
        }else{
            $parameter['name']='';
        }
        if(isset($is_pay) && $is_pay!=="" ){
            $parameter['is_pay']=$is_pay;
        }else{
            $parameter['is_pay']='';
        }
        if(isset($status) && $status!==""){
            $parameter['status']=$status;
        }else{
            $parameter['status']='';
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
    //运动处方表单
    public function motion()
    {
        $o_id=Request::instance()->param('o_id','','strip_tags,strtolower');
        $model=new Motion_order();
        $data=$model->getUser($o_id);
        $this->assign('data',$data);
        return view('motion');
    }
    //生成运动处方
    public function create()
    {
        Request::instance()->param('name','','strip_tags,strtolower');
        $add['o_id']=Request::instance()->param('o_id','','strip_tags,strtolower');
        $add['u_id']=Request::instance()->param('u_id','','strip_tags,strtolower');
        $add['sex']=Request::instance()->param('sex','','strip_tags,strtolower');
        $add['number']=Request::instance()->param('number','','strip_tags,strtolower');
        $add['week']=Request::instance()->param('week','','strip_tags,strtolower');
        $add['birth']=Request::instance()->param('birth','','strip_tags,strtolower');
        $add['age']=Request::instance()->param('age','','strip_tags,strtolower');
        $add['boneage']=Request::instance()->param('boneage','','strip_tags,strtolower');
        $add['grow']=Request::instance()->param('grow','','strip_tags,strtolower');
        $add['height']=Request::instance()->param('height','','strip_tags,strtolower');
        $add['weight']=Request::instance()->param('weight','','strip_tags,strtolower');
        $add['heartrate']=Request::instance()->param('heartrate','','strip_tags,strtolower');
        $add['blank']=Request::instance()->param('blank','','strip_tags,strtolower');
        $add['pressure']=Request::instance()->param('pressure','','strip_tags,strtolower');
        $add['lowlimit']=Request::instance()->param('lowlimit','','strip_tags,strtolower');
        $add['highlimit']=Request::instance()->param('highlimit','','strip_tags,strtolower');
        $add['objective']=Request::instance()->param('objective','','strip_tags,strtolower');
        $add['project']=Request::instance()->param('project','','strip_tags,strtolower');
        $add['exerciseintensity']=Request::instance()->param('exerciseintensity','','strip_tags,strtolower');
        $add['readytoexercise']=Request::instance()->param('readytoexercise','','strip_tags,strtolower');
        $add['basicmovement']=Request::instance()->param('basicmovement','','strip_tags,strtolower');
        $add['finishingmovement']=Request::instance()->param('finishingmovement','','strip_tags,strtolower');
        $add['timeofmovement']=Request::instance()->param('timeofmovement','','strip_tags,strtolower');
        $add['weeklyexercisetime']=Request::instance()->param('weeklyexercisetime','','strip_tags,strtolower');
        $add['expertsignature']=Request::instance()->param('expertsignature','','strip_tags,strtolower');
        $add['year']=Request::instance()->param('year','','strip_tags,strtolower').'年';
        $month=Request::instance()->param('month','','strip_tags,strtolower');
        $day=Request::instance()->param('day','','strip_tags,strtolower');
        if(!empty($month)){
            $add['year'].=$month.'月';
            if(!empty($day)){
                $add['year'].=$day.'日';
            }
        }
        $model=new Motion_order();
        $str=$model->addWay($add);
        if($str==1){
            $save=$model->saveOrder($add['o_id']);
            if($save>0){
                $this->success('处方已生成','Motion/index',3);
            }else{
                $this->success('修改失败','Motion/index',3);
            }
        }else{
            $this->success('添加失败','Motion/index',3);
        }
    }
    //查看处方
    public function look()
    {
        $o_id=Request::instance()->param('order_id','','strip_tags,strtolower');
        $model=new Motion_order();
        $data=$model->allWay($o_id);
        $this->assign('data',$data);
        return view('look');
    }

}