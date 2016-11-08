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
        $where['type']=1;
        $count = Db::table('order')->where($where)->count();
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
        $order = Db::table('nutrition_order')
        ->alias('n')
        ->join('order o','o.o_id = n.o_id')
        ->join('gl_users g ','o.u_id = g.id ')
        ->where('o.type ','1')
        ->where('o.out_trade_no','like',"%$n_number%")
        ->where('g.name','like',"%$name%")
        ->where('o.status','like',"%$status%")
        ->where('o.is_pay','like',"%$is_pay%")
        ->order("o.addtime desc")
        ->field('o.o_id,o.out_trade_no,o.status,o.addtime,o.is_pay,g.name')
        ->limit("$start,10")
        ->select();
        // echo DB::getlastsql();die;
        $this->assign('page',$parameter);
        $this->assign('name',$name);
        $this->assign('n_number',$n_number);
        $this->assign('is_pay',$is_pay);
        $this->assign('status',$status);
        $this->assign('order',$order);
        return view('index');
    }

    public function getuserdiet()
    {
    	//获取用户ID
    	$user_id = Request::instance()->param('id','','strip_tags,strtolower');
    	//查询用户的记录
    	$data = Db::field('an_id,u_date')->table('user_answer')->where('u_id',$user_id)->order('u_date desc')->select();
    	$this->assign("data",$data);
    	return view('userdiet');
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