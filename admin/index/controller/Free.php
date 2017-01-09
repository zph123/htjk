<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;
use app\index\model\Motion_orders;

class Free extends Common
{
    //首页
    public function nutrition()
    {
        $out_trade_no = Request::instance()->get('out_trade_no');
        $is_pay=Request::instance()->get('is_pay');
        $status=Request::instance()->get('status');
        $page=Request::instance()->get('page');
        $id_number=Request::instance()->get('id_number');
        $where=array();
        if(!empty($out_trade_no)){
            $where['out_trade_no']=$out_trade_no;
        }
        if(!empty($id_number)){
            $where['u.id_number']=$id_number;
        }        
        if(isset($status) && $status!==""){
            $where['status']=$status;
        }
        $where['type']=1;
        $count = Db::table('order')
                ->alias('o')
                ->join('gl_users g ','o.u_id = g.id ')
                ->join('user_infos u ','u.u_id = g.id ')
                ->where($where)->count();
        $paging=10;
        $leaf=ceil($count/$paging);
        $page=isset($_GET['page'])?$_GET['page']:1;
        $start=($page-1)*$paging;
        $lastpage=$page-1<1?1:$page-1;
        $nextpage=$page+1>$leaf?$leaf:$page+1;
        $parameter['page']    =$page;
        $parameter['nextpage']=$nextpage;
        $parameter['lastpage']=$lastpage;
        $parameter['leaf']    = $leaf;

        $name= Request::instance()->get('name');
        $n_number = Request::instance()->get('n_number');
        $is_pay= Request::instance()->get('is_pay');
        $status = Request::instance()->get('status');
        $order = Db::table('order')
        ->alias('o')
        ->join('gl_users g ','o.u_id = g.id ')
        ->join('user_infos u ','u.u_id = g.id ')
        ->where('o.type','1')
        ->where('o.is_charge','1')
        ->where('o.out_trade_no','like',"%$n_number%")
        ->where('g.name','like',"%$name%")
        ->where('o.status','like',"%$status%")
        ->where('o.is_pay','like',"%$is_pay%")
        ->where('u.id_number','like',"%$id_number%")
        ->order("o.addtime desc")
        ->field('u.id_number,o.o_id,o.out_trade_no,o.status,o.addtime,o.is_pay,g.name')
        ->limit("$start,10")
        ->select();

        //echo DB::getlastsql();die;
        $this->assign('page',$parameter);
        $this->assign('name',$name);
        $this->assign('n_number',$n_number);
        $this->assign('status',$status);
        $this->assign('id_number',$id_number);
        $this->assign('order',$order);
        return view('index'); 
    }
    public function userdiet()
    {
    	// //获取用户ID
    	// $user_id = Request::instance()->param('id','','strip_tags,strtolower');
    	// //查询用户的记录
    	// $data = Db::field('an_id,u_date')->table('user_answer')->where('u_id',$user_id)->order('u_date desc')->select();
    	// $this->assign("data",$data);
    	// return view('userdiet');

        $o_id=Request::instance()->param('o_id','','strip_tags,strtolower');
        $res = Db::table('nutrition_way')->where('o_id',$o_id)->find();
        if($res)
        {
          $this->error('此订单已经生成报告','free/dietdetails?id='.$o_id,3);
        }
        $data = Db::table('order')
        ->alias('o')
        ->field('out_trade_no,addtime,is_pay,status,o_id,name,sex,year,u_id')
        ->join('gl_users g ', 'o.u_id = g.id ')
        ->where('type', 1)
        ->where('o_id', $o_id)
        ->select();
        // print_r($week)
        $this->assign('data',$data);
        return view('userdiet');
    }

    public function create()
    {
        $data = input('post.');
        $res = Db::table('nutrition_way')->where('o_id',$data['o_id'])->find();
        if($res)
        {
          $this->error('此订单已经生成报告','free/dietdetails?id='.$o_id,3);
        }

        $res = Db::table('nutrition_way')->insert($data);
        if($res)
        {
          $re =  Db::table('order')->where('o_id',$data['o_id'])->setField('status', '1');
          if($re)
          {
            $this->success('报告生成成功','free/dietdetails?id='.$data['o_id'],3);
          }
          else
          {
            $this->error('报告生成失败','free/dietdetails?id='.$data['o_id'],3);
          }
        }
    }

    public function dietshow()
    {
        

       $o_id=Request::instance()->param('o_id','','strip_tags,strtolower');

       $res = Db::table('nutrition_way')->where('o_id',$o_id)->find();
        if(!$res)
        {
          $this->error('该订单没有报告！','free/dietdetails?id='.$o_id,3);
        }
        
       $data = Db::table('order')
        ->alias('o')
        ->field('out_trade_no,addtime,is_pay,status,name,sex,year,u_id,n.*')
        ->join('gl_users g ', 'o.u_id = g.id ')
        ->join('nutrition_way n ', 'n.o_id = o.o_id ')
        ->where('type', 1)
        ->where('o.o_id', $o_id)
        ->select();
        $this->assign('data',$data);
        return view('dietshow');
    }

    public function dietdetails()
    {

        //获取答卷ID
        $id = Request::instance()->param('id','','strip_tags,strtolower');
        $data = Db::table('nutrition_order')
        ->alias('n')
        ->join('order o','o.o_id = n.o_id')
        ->join('gl_users g ','o.u_id = g.id ')
        ->where('o.o_id = '.$id)
        ->field('o.o_id,o.out_trade_no,o.status,o.addtime,g.phone,o.is_pay,n.desc,g.name')
        ->find();
        $data['desc'] = json_decode($data['desc'],true);
        $week = array();   //饮食习惯评价
        $weeks = array(); //每周进食次数评价
       // print_r($data);die;
        foreach ($data['desc'][1] as $key=>$value) {
            $day[$key] = $value;
        }
        if(isset($data['desc'][2]))
        {
          foreach ($data['desc'][2] as $key=>$value) {
              $week[$key] = $value;
          } 
        }  
        if(isset($data['desc'][3]))
        {
          foreach ($data['desc'][3] as $key=>$value) {
              $weeks[$key] = $value;
          } 
        }
        //print_r($weeks);die;           
        $this->assign("weeks",$weeks);
        $this->assign("week",$week);
        $this->assign("day",$day);
        $this->assign("data",$data);
        return view('dietdetails');
   //  	//获取答卷ID
   //  	$id = Request::instance()->param('id','','strip_tags,strtolower');
   //  	$data = Db::field('u_answer,u_desc')->table('user_answer')->where('an_id',$id)->find();
   //  	$answer = json_decode($data['u_answer'],true);
   //  	$str = '';
   //  	foreach ($answer as $key => $val) {
   //  		if(is_array($val))
   //  		{
   //  			foreach ($val as $k => $v) {
   //  				$str.=$v.',';
   //  			}
   //  		}
			// else
			// {
			// 	$str .= $val.',';
			// }
    		
   //  	}
   //  	$str=rtrim($str,',');
   //  	$details = Db::field('q.q_id,q.q_content,a.a_content,GROUP_CONCAT(a.a_content) as a_content')->table('question_answer')->alias('qa')->join('question q','qa.q_id = q.q_id')->join('answer a','qa.a_id = a.a_id')->where('qa.qa_id','in',"$str")->group('q.q_id')->select();
   //  	//问答题
   //  	if($data['u_desc']!="")
   //      {
   //          $desc = json_decode($data['u_desc'],true);
   //          $str ="";
   //          foreach ($desc as $key => $val) {
   //              $str .= $key.',';
   //          }
   //          $desc_question = Db::field('q_id,q_content')->table('question')->alias('q')->where('q.q_id','in',"$str")->select();
   //          foreach ($desc_question as $key => $val) {
   //              $desc_question[$key]['a_content'] = $desc[$val['q_id']];
   //          }
   //          $this->assign("desc_question",$desc_question);
   //      }
    	
   //  	$this->assign("details",$details);
   //  	return view('dietdetails');
    }

    public function motions(){
        $name = Request::instance()->get('name');
        $out_trade_no = Request::instance()->get('out_trade_no');
        $id_number=Request::instance()->get('id_number');
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
        if(isset($status) && $status!==""){
            $where['status']=$status;
        }
        $model = new Motion_orders();
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
        if(!empty($id_number)){
            $parameter['$id_number']=$id_number;
        }else{
            $parameter['name']='';
        }
        if(isset($status) && $status!==""){
            $parameter['status']=$status;
        }else{
            $parameter['status']='';
        }

        $this->assign('page',$parameter);
        $this->assign('data',$data);
        return view('motions');    	
    }

    //查看详情
    public function motiondetails()
    {
        $u_id=Request::instance()->param('u_id','','strip_tags,strtolower');
        $order_id=Request::instance()->param('order_id','','strip_tags,strtolower');
        $begin_time=Request::instance()->param('begin_time','','strip_tags,strtolower');
        $end_time=Request::instance()->param('end_time','','strip_tags,strtolower');








        
        $model=new Motion_orders();
        $data=$model->details($u_id,$begin_time,$end_time);
        $this->assign('data',$data);
        $one=$model->getOne($u_id,$order_id);
        $this->assign('one',$one);

        $date=$model->getUser($u_id);
        //var_dump($date);exit;
        $date['weight']=json_decode($date['weight'],true);
        $date['height']=json_decode($date['height'],true);
        $date['gpbone']=json_decode($date['gpbone'],true);
        $this->assign('date',$date);
        return view('motiondetails');
    }
    //运动处方表单
    public function motion()
    {
        $o_id=Request::instance()->param('o_id','','strip_tags,strtolower');
        $model=new Motion_orders();
        $data=$model->getUser($o_id);
        $this->assign('data',$data);
        return view('motion');
    }  

    //生成运动处方
    public function motioncreate()
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
        $add['yearw']=Request::instance()->param('year','','strip_tags,strtolower').'年';
        $month=Request::instance()->param('month','','strip_tags,strtolower');
        $day=Request::instance()->param('day','','strip_tags,strtolower');
        if(!empty($month)){
            $add['yearw'].=$month.'月';
            if(!empty($day)){
                $add['yearw'].=$day.'日';
            }
        }
        $model=new Motion_order();
        $str=$model->addWay($add);
        if($str==1){
            $save=$model->saveOrder($add['o_id']);
            if($save>0){
                $this->success('处方已生成','free/motions',3);
            }else{
                $this->success('修改失败','free/motions',3);
            }
        }else{
            $this->success('添加失败','free/motions',3);
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