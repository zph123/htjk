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

    public function question($data=0){
    	if($data==0){
    		$this->error('参数错误');
    	}
		$time = strtotime($data);
		if($time>time()){
			$this->error('未来还没到哦~');
		}

    	$week = date('w',strtotime($data));
    	$firstday = date("Y-m-01",strtotime($data));
 		$lastday = date("Y-m-d",strtotime("$firstday 1 month -1 day"));
 		if($lastday==$data){
 			$num = 3;
 		}elseif($week==0){
 			$num = 2;
 		}else{
			$num = 1;
 		}
		//查询用户填过的数据
		$info = Db::table('user_answer')->where(['u_id'=>1,'u_date'=>$data])->find();
		$anwser = json_decode($info['u_answer'],true);
		$text = json_decode($info['u_desc'],true);
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
			return view("answer",['data'=>$tmp,'date'=>$data,'text'=>$text]);
		}
    	$res = Db::table('answer')
			->alias('a')
    		->field('a_content,q_content,p_id,qa_id,rc_type,q.q_id')
			->join('question_answer qa','a.a_id = qa.a_id')
			->join('question q','qa.q_id = q.q_id')
			->where('q_type',$num)
			->select();
		$group = Db::table('question_answer')->alias('qa')->field('p_id')->join('question q','qa.q_id = q.q_id')->group('p_id')->where('q_type',$num)->select();

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
    	return view('question',['data'=>$arr,'date'=>$data,'anwser'=>$anwser,'text'=>$text,'status'=>$info['status']]);
    }

	/**
	 * 递归查询出所有儿子
	 * @param $son
	 * @return array
	 */
    public function digui($son){
    	$res = Db::table('question_answer')
    		->field('p_id')
			->where('q_id',$son)
			->where('p_id','neq',0)
			->group('p_id')
			->select();
		$newarr = array();
		foreach ($res as $k=>$v){
			$newarr = array_merge($res,$this->digui($v['p_id']));
		}
		return $newarr;
    }

	public function ajax($son)
	{
		$arr = $this->digui($son);
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
}