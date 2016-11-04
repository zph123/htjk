<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;

class Nutrientbalance extends Controller
{
	public function _initialize()
    {
        //初始化，以后设计用
    }

	/**
	 * 答卷渲染
	 * @param int $data string Y-m-d格式的日期
	 * @return \think\response\View 视图 答题视图
	 */
    public function question($date=0){
    	if($date==0){
    		$this->error('参数错误');
    	}
		$time = strtotime($date);
		if($time>time()){
			$this->error('未来还没到哦~');
		}

    	$week = date('w',strtotime($date));
    	$firstday = date("Y-m-01",strtotime($date));
 		$lastday = date("Y-m-d",strtotime("$firstday 1 month -1 day"));
 		if($lastday==$date){
 			$num = 3;
 		}elseif($week==0){
 			$num = 2;
 		}else{
			$num = 1;
 		}
		//查询用户填过的数据
		$info = Db::table('user_answer')->where(['u_id'=>1,'u_date'=>$date])->find();
		$anwser = json_decode($info['u_answer'],true);
		$text = json_decode($info['u_desc'],true);
		//用户已经选完 返回不可选择的页面
		if($info['status']==1) {
			$map = array();
			foreach ($anwser as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $key => $val) {
						$map[] = $val;
					}
				} else {
					$map[] = $v;
				}
			}
			$user = Db::table('question_answer')
				->alias('qa')
				->field('a_content,q_content,q.q_id')
				->join('answer a', 'a.a_id = qa.a_id')
				->join('question q', 'qa.q_id = q.q_id')
				->where('qa.qa_id', 'in', $map)
				->select();
			$tmp = array();
			foreach ($user as $key => $value) {
				$tmp[$value['q_id']]['question'] = $value['q_content'];
				$tmp[$value['q_id']]['anwser'][] = $value['a_content'];
			}
			//模板赋值 data 用户问题和答案 date 查询时间 text 用户注明文字
			return view("answer",['data'=>$tmp,'date'=>$date,'text'=>$text]);
		}
    	$res = Db::table('answer')
			->alias('a')
    		->field('a_content,q_content,p_id,qa_id,rc_type,q.q_id')
			->join('question_answer qa','a.a_id = qa.a_id')
			->join('question q','qa.q_id = q.q_id')
			->where('q_type',$num)
			->select();

		//处理数组成方便前台渲染的数据
		$arr = array();
		foreach ($res as $key => $value) {
			$arr[$value['q_id']]['question'] = $res[$key]['q_content'];
			$arr[$value['q_id']]['rc_type']  = $res[$key]['rc_type'];
			$arr[$value['q_id']]['son'][] = $res[$key]['p_id'];
			$arr[$value['q_id']]['answer'][$res[$key]['qa_id']] = array(
				'content'	=>	$res[$key]['a_content'],
				'p_id'		=>  $res[$key]['p_id'],
			);
			$arr[$value['q_id']]['son'] = array_unique($arr[$value['q_id']]['son']);
			foreach ($arr[$value['q_id']]['son'] as $k=>$v){
				if($v==0){
					unset($arr[$value['q_id']]['son'][$k]);
				}else {
					if (isset($arr[$arr[$value['q_id']]['son'][$k]])) {
						$new = array(
							$arr[$value['q_id']]['son'][$k] => $arr[$arr[$value['q_id']]['son'][$k]],
						);
						unset($arr[$arr[$value['q_id']]['son'][$k]]);
						$arr[$arr[$value['q_id']]['son'][$k]] = $new[$arr[$value['q_id']]['son'][$k]];
					}
					$arr[$arr[$value['q_id']]['son'][$k]]['dis'] = 1;
				}
			}
		}
		//模板赋值 data 问题 date 时间日期 anwser 用户选项 text 用户注明文字 status 答题状态：1已完成 0未完成
    	return view('question',['data'=>$arr,'date'=>$date,'anwser'=>$anwser,'text'=>$text,'status'=>$info['status']]);
    }

	/**
	 * 递归查询出所有儿子
	 * @param $son integer 传入的问题ID
	 * @return array 所有子孙数组
	 */
    public function recursion($son){
    	$res = Db::table('question_answer')
    		->field('p_id')
			->where('q_id',$son)
			->where('p_id','neq',0)
			->group('p_id')
			->select();
		$newarr = array();
		foreach ($res as $k=>$v){
			$newarr = array_merge($res,$this->recursion($v['p_id']));
		}
		return $newarr;
    }

	/**
	 * 接口 请求返回p_id数组
	 * @param $son integer 传入问题ID
	 * @return array 所有子孙数组
	 */
	public function ajax($son)
	{
		$arr = $this->recursion($son);
		$data = array();
		foreach ($arr as $k=>$v){
			$data[] = $v['p_id'];
		}
		if (Request::instance()->isAjax()){
			echo json_encode($data);
		}else{
			return $data;
		}

	}

    public function add_do(Request $request){

		$res = Db::table('question_answer')
			->field('q_id')
			->where('p_id','neq',0)
			->group('p_id')
			->select();
		$arr = array();
		foreach($res as $v){
			$arr[] = $v["q_id"];
		}
		$res = Db::table('question_answer')
			->field('qa_id')
			->where('p_id','eq',0)
			->where('q_id','in',$arr)
			->group('q_id')
			->select();
		$data = $request->post();
		$dates = array();
		foreach ($data['anwser'] as $key=> $val){
			foreach($res as $v){
				if($val==$v['qa_id']){
					$dates[] = $this->ajax($key);
				}
			}
		}
		foreach ($dates as $key=>$value){
			foreach ($value  as $k=>$v){
				if(isset($data['anwser'][$v])){
					unset($data['anwser'][$v]);
				}
			}
		}
    	$mysql_data['u_answer'] = json_encode($data['anwser']);
		//用户ID
		$mysql_data['u_id'] = 1;
		$mysql_data['u_date'] = $data['date'];
		if(isset($data['text'])){
			$mysql_data['u_desc'] = json_encode($data['text']);
		}
		$week = date('w',strtotime($data['date']));
		$firstday = date("Y-m-01",strtotime($data['date']));
		$lastday = date("Y-m-d",strtotime("$firstday 1 month -1 day"));
		if($lastday==$data['date']){
			$num = 3;
		}elseif($week==0){
			$num = 2;
		}else{
			$num = 1;
		}
		$number = count($data['anwser']);
		$count = Db::table('question')->where('q_type',$num)->count();
		if($num==2){
			$count = $count-2;
		}
		if($count<=$number){
			$mysql_data['status'] = 1;
		}
		//用户ID
		$map['u_id'] = 1;
		$map['u_date'] = $data['date'];
		$info = Db::table('user_answer')->where($map)->find();
		if($info){
			$res = Db::table('user_answer')->where($map)->update($mysql_data);
		}else {
			$res = Db::table('user_answer')->insert($mysql_data);
		}
		if($res){
			$this->success('成功','index/nutrientbalance');
		}else{
			$this->error('失败');
		}

    }

	public function getcolor($date=0) {
		if($date==0){
			echo "error";
		}
		$firstday = date("Y-m-01",strtotime($date));
		$lastday = date("Y-m-d",strtotime("$firstday 1 month -1 day"));
		$res = Db::table('user_answer')->field('u_date,status')->where('u_date','between',[$firstday,$lastday])->where('u_id',1)->select();
		$arr  = array();
		foreach ($res as $k=>$v){
			$arr[date('j',strtotime($v['u_date']))] = $v['status'];
		}
		echo json_encode($arr);
	}

	public function getdays($s_time,$e_time){
		if($s_time>$e_time){
			echo 0;die;
		}
		$res = Db::table('user_answer')->where('u_date','between',[$s_time,$e_time])->where('u_id',1)->where('status',1)->count();
		echo $res;
	}

	public function generateorder(Request $request){
		$s_time = $request->post("s_time");
		$e_time = $request->post("e_time");
		$res = Db::table('user_answer')
			->field('u_date,u_answer,u_desc')
			->where('u_date','between',[$s_time,$e_time])
			->where('u_id',1)
			->where('status',1)
			->select();
		foreach ($res as $ke=>$value) {
			$answer = json_decode($value['u_answer'], true);
			$str = '';
			foreach ($answer as $key => $val) {
				if (is_array($val)) {
					foreach ($val as $k => $v) {
						$str .= $v . ',';
					}
				} else {
					$str .= $val . ',';
				}

			}
			$str = rtrim($str, ',');
			$details[$value['u_date']] = Db::field('q.q_id,q.q_content,a.a_content,GROUP_CONCAT(a.a_content) as a_content')
				->table('question_answer')
				->alias('qa')
				->join('question q','qa.q_id = q.q_id')
				->join('answer a','qa.a_id = a.a_id')
				->where('qa.qa_id','in',"$str")
				->group('q.q_id')
				->select();

			//问答题
			if($value['u_desc']!="")
			{
				$desc = json_decode($value['u_desc'],true);
				$str ="";
				foreach ($desc as $key => $val) {
					$str .= $key.',';
				}
				$desc_question = Db::field('q_id')
					->table('question')
					->alias('q')
					->where('q.q_id','in',"$str")
					->select();
				foreach ($desc_question as $key => $val) {
					$desc_question[$key]['a_content'] = $desc[$val['q_id']];
				}
				$descs[$value['u_date']] = $desc_question;
			}
		}

		$detail = array();
		if(!isset($details)) {
			echo 4;die;
		}
		foreach ($details as $key => $value) {
			foreach ($value as $k => $v) {
				$detail[$key][$v['q_id']] = array(
					'question' => $v['q_content'],
					'answer' => $v['a_content'],
				);
			}
		}
		if (isset($descs)) {
			foreach ($descs as $key => $value) {
				foreach ($value as $k => $v) {
					if ($v['a_content'] != '') {
						$detail[$key][$v['q_id']]['desc'] = $v['a_content'];
					}
				}
			}
		}
		//用户ID
		$mysql_date['u_id'] = 1;
		$mysql_date['o_desc'] = json_encode($detail);
		$mysql_date['add_time'] = date("Y-m-d H:i;s",time());
	}
}