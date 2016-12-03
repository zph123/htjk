<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;

class Diet extends Common
{
    //首页
    public function index()
    {
    	//查询填写答卷的用户
    	// $user = Db::field('g.name,g.id')->table('user_answer')->alias('u')->join('gl_users g ',' g.id = u.u_id')->group('u.u_id')->select();
    	// $this->assign("user",$user);
        // 查询订单
        // 
        // 
        // $arr = $user->two_select($where);
        // $number=count($arr);
        // $paging=5;
        // $leaf=ceil($number/$paging);
        // $page=isset($_GET['page'])?$_GET['page']:1;
        // $start=($page-1)*$paging;
        // $lastpage=$page-1<1?1:$page-1;
        // $nextpage=$page+1>$leaf?$leaf:$page+1;
        // $data = $user->on_select($where,$start);
        // if(!empty($users))
        // {
        //     $parameter=array("where"=>$users,"page"=>$page,"nextpage"=>$nextpage,"lastpage"=>$lastpage,"leaf"=>$leaf);
        // }
        // else
        // {
        //     $parameter=array("page"=>$page,"nextpage"=>$nextpage,"lastpage"=>$lastpage,"leaf"=>$leaf);
        // }

        // $this->assign('page',$parameter);
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
        if(isset($is_pay) && $is_pay!=="" ){
            $where['is_pay']=$is_pay;
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
        ->where('type','1')
        ->where('o.is_charge','0')
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
        $this->assign('is_pay',$is_pay);
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
          $this->error('此订单已经生成报告','diet/dietdetails?id='.$o_id,3);
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
          $this->error('此订单已经生成报告','diet/dietdetails?id='.$o_id,3);
        }

        $res = Db::table('nutrition_way')->insert($data);
        if($res)
        {
          $re =  Db::table('order')->where('o_id',$data['o_id'])->setField('status', '1');
          if($re)
          {
            $this->success('报告生成成功','diet/dietdetails?id='.$data['o_id'],3);
          }
          else
          {
            $this->error('报告生成失败','diet/dietdetails?id='.$data['o_id'],3);
          }
        }
    }

    public function dietshow()
    {
        

       $o_id=Request::instance()->param('o_id','','strip_tags,strtolower');

       $res = Db::table('nutrition_way')->where('o_id',$o_id)->find();
        if(!$res)
        {
          $this->error('该订单没有报告！','diet/dietdetails?id='.$o_id,3);
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

}