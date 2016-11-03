<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use \think\Request;

class Diet extends Controller
{
    public function _initialize()
    {
        //初始化，以后设计用
    }
    //首页
    public function index()
    {
    	//查询填写答卷的用户
    	$user = Db::field('g.name,g.id')->table('user_answer')->alias('u')->join('gl_users g ',' g.id = u.u_id')->group('u.u_id')->select();
    	$this->assign("user",$user);
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
    	$data = Db::field('u_answer,u_desc')->table('user_answer')->where('an_id',$id)->find();
    	$answer = json_decode($data['u_answer'],true);
    	$str = '';
    	foreach ($answer as $key => $val) {
    		if(is_array($val))
    		{
    			foreach ($val as $k => $v) {
    				$str.=$v.',';
    			}
    		}
			else
			{
				$str .= $val.',';
			}
    		
    	}
    	$str=rtrim($str,',');
    	$details = Db::field('q.q_id,q.q_content,a.a_content,GROUP_CONCAT(a.a_content) as a_content')->table('question_answer')->alias('qa')->join('question q','qa.q_id = q.q_id')->join('answer a','qa.a_id = a.a_id')->where('qa.qa_id','in',"$str")->group('q.q_id')->select();
    	//问答题
    	$str ="";
    	$desc = json_decode($data['u_desc'],true);
    	foreach ($desc as $key => $val) {
			$str .= $key.',';
    	}
    	$desc_question = Db::field('q_id,q_content')->table('question')->alias('q')->where('q.q_id','in',"$str")->select();
    	foreach ($desc_question as $key => $val) {
    		$desc_question[$key]['a_content'] = $desc[$val['q_id']];
    	}
    	$this->assign("details",$details);
    	$this->assign("desc_question",$desc_question);
    	return view('dietdetails');
    }

}