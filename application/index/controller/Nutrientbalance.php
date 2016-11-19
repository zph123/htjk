<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
use app\index\model\Order;
use think\Cookie;

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
    public function question( $date = 0, $num = 1) {

		if( !Session::has('uid') ) {
			$this->redirect("login/index");
		}

		$u_id = Session::get('uid');

		$username = Db::table('gl_users')->field('name')->where('id',$u_id)->find();

    	if($date == 0) {
    		$this->error('参数错误');
    	}
		$time = strtotime($date);
		if($time === false) {
			$this->error('日期不正确');
		}
		if($time > time() ) {
			$this->error('未来还没到哦~');
		}
		//查询上一周周日填完没
		$week = date('w',strtotime($date));
		$lastsunday = '';
		switch ($week){
			case 0:
				$lastsunday = date('Y-m-d',strtotime("$date -7 day"));
				break;
			case 1:
				$lastsunday = date('Y-m-d',strtotime("$date -1 day"));
				break;
			case 2:
				$lastsunday = date('Y-m-d',strtotime("$date -2 day"));
				break;
			case 3:
				$lastsunday = date('Y-m-d',strtotime("$date -3 day"));
				break;
			case 4:
				$lastsunday = date('Y-m-d',strtotime("$date -4 day"));
				break;
			case 5:
				$lastsunday = date('Y-m-d',strtotime("$date -5 day"));
				break;
			case 6:
				$lastsunday = date('Y-m-d',strtotime("$date -6 day"));
				break;
		}
		$count = Db::table("user_answer")->where(['u_id'=>$u_id,'u_date'=>$lastsunday])->count();
		$lastmonday = date('Y-m-d',strtotime("$lastsunday -6 day"));
		$zong = Db::table("user_answer")->where('u_id',$u_id)->where('u_date','between',[$lastmonday,$lastsunday])->count();
		if($zong!=0 && $count<3){
			$this->error('请填写上周周报');
		}


		//查询用户填过的数据
		$info = Db::table('user_answer')
			->where([
				'u_id'=>$u_id,
				'u_date'=>$date,
				'type'=>$num
			])
			->find();
		$anwser = json_decode($info['u_answer'], true);
		$text = json_decode($info['u_desc'], true);
//		var_dump($info);die;
//		//用户已经选完 返回不可选择的页面
//		if($info['status'] == 1) {
//
//			$map = array();
//			foreach ($anwser as $k => $v) {
//				if ( is_array( $v ) ) {
//					foreach ($v as $key => $val) {
//						$map[] = $val;
//					}
//				} else {
//					$map[] = $v;
//				}
//			}
//
//			$user = Db::table('question_answer')
//				->alias('qa')
//				->field('a_content,q_content,q.q_id')
//				->join('answer a', 'a.a_id = qa.a_id')
//				->join('question q', 'qa.q_id = q.q_id')
//				->where('qa.qa_id', 'in', $map)
//				->select();
//
//			$tmp = array();
//			foreach ($user as $key => $value) {
//				$tmp[$value['q_id']]['question'] = $value['q_content'];
//				$tmp[$value['q_id']]['anwser'][] = $value['a_content'];
//			}
//
//			//模板赋值 data 用户问题和答案 date 查询时间 text 用户注明文字
//			return view("answer",[ 'data' => $tmp, 'date' => $date, 'text' => $text, 'username' => $username['name'] ]);
//		}

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
			$arr[$value['q_id']]['son'][] = $res[ $key ]['p_id'];
			$arr[$value['q_id']]['son'] = array_unique( $arr[$value['q_id']]['son'] );
			$arr[$value['q_id']]['answer'][$res[$key]['qa_id']] = array(
				'content'	=>	$res[$key]['a_content'],
				'p_id'		=>  $res[$key]['p_id'],
			);

			foreach ($arr[$value['q_id']]['son'] as $k => $v){
				if( $v == 0 ) {
					unset($arr[$value['q_id']]['son'][$k]);
				} else {

					if ( isset($arr[$arr[$value['q_id']]['son'][$k]]) ) {
						$new = array(
							$arr[$value['q_id']]['son'][$k] => $arr[$arr[$value['q_id']]['son'][$k]],
						);
						unset( $arr[$arr[$value['q_id']]['son'][$k]] );
						$arr[$arr[$value['q_id']]['son'][$k]] = $new[$arr[$value['q_id']]['son'][$k]];
					}
					$arr[$arr[$value['q_id']]['son'][$k]]['dis'] = 1;

				}
			}
		}


		//模板赋值 data 问题 date 时间日期 anwser 用户选项 text 用户注明文字 status 答题状态：1已完成 0未完成
    	return view('question', [ 'data' => $arr, 'date' => $date, 'anwser' => $anwser, 'text' => $text, 'status' => $info['status'], 'username' => $username['name'], 'type'=>$num ]);
    }

	/**
	 * 递归查询出所有儿子
	 * @param $son integer 传入的问题ID
	 * @return array 所有子孙数组
	 */
    private function recursion( $son ) {

		if( !is_numeric($son) ) {
			return array();
		}

    	$res = Db::table('question_answer')
    		->field('p_id')
			->where('q_id', $son)
			->where('p_id', 'neq', 0)
			->group('p_id')
			->select();

		$newarr = array();
		foreach ($res as $k => $v) {
			$newarr = array_merge($res, $this->recursion($v['p_id']));
		}

		return $newarr;
    }

	/**
	 * @param int $date 日期 date
	 */
	public function getweek($date = 0)
	{
		if (!Request::instance()->isAjax()) {
			echo 4;die;
		}
		if( !Session::has('uid') ) {
			echo 2;die;
		}
		$u_id = Session::get('uid');
		//查询上一周周日填完没
		$week = date('w',strtotime($date));
		$lastsunday = '';
		switch ($week){
			case 0:
				$lastsunday = date('Y-m-d',strtotime("$date -7 day"));
				break;
			case 1:
				$lastsunday = date('Y-m-d',strtotime("$date -1 day"));
				break;
			case 2:
				$lastsunday = date('Y-m-d',strtotime("$date -2 day"));
				break;
			case 3:
				$lastsunday = date('Y-m-d',strtotime("$date -3 day"));
				break;
			case 4:
				$lastsunday = date('Y-m-d',strtotime("$date -4 day"));
				break;
			case 5:
				$lastsunday = date('Y-m-d',strtotime("$date -5 day"));
				break;
			case 6:
				$lastsunday = date('Y-m-d',strtotime("$date -6 day"));
				break;
		}
		$count = Db::table("user_answer")->where(['u_id'=>$u_id,'u_date'=>$lastsunday])->count();
		$lastmonday = date('Y-m-d',strtotime("$lastsunday -6 day"));
		$zong = Db::table("user_answer")->where('u_id',$u_id)->where('u_date','between',[$lastmonday,$lastsunday])->count();
		if($zong!=0 && $count<3){
			echo 1;
		}else{
			echo 0;
		}
	}

	/**
	 * 接口 请求返回p_id数组
	 * @param $son integer 传入问题ID
	 * @return array 所有子孙数组
	 */
	public function ajax( $son ) {

		if( !is_numeric($son) ) {
			return array();
		}

		$arr = $this->recursion($son);

		$data = array();
		foreach ($arr as $k => $v) {
			$data[] = $v['p_id'];
		}

		if (Request::instance()->isAjax()) {
			echo json_encode($data);
		} else {
			return $data;
		}

	}

	/**
	 * 添加 修改答卷 接收表单参数
	 * @param Request $request
	 * @throws \think\Exception
	 */
    public function add_do(Request $request) {

		if (!Request::instance()->isPost()) {
			$this->redirect('index/nutrientbalance');
		}

		if( !Session::has('uid') ) {
			$this->redirect("login/index");
		}
		$u_id = Session::get('uid');

		$data = $request->post();
		if(!isset($data['anwser'])){
			$this->redirect('index/nutrientbalance');
		}
		if( !is_array($data['anwser']) ) {
			$this->redirect('index/nutrientbalance');
		}
		if( strtotime($data['date']) === false) {
			$this->redirect('index/nutrientbalance');
		}

		$res = Db::table('question_answer')
			->field('q_id')
			->where('p_id', 'neq', 0)
			->group('p_id')
			->select();

		$arr = array();
		foreach($res as $v) {
			$arr[] = $v["q_id"];
		}

		$res = Db::table('question_answer')
			->field('qa_id')
			->where('p_id', 'eq', 0)
			->where('q_id', 'in', $arr)
			->group('q_id')
			->select();

		$dates = array();
		foreach ($data['anwser'] as $key => $val) {
			foreach($res as $v) {
				if($val == $v['qa_id']) {
					$dates[] = $this->ajax($key);
				}
			}
		}
		foreach ($dates as $key => $value) {
			foreach ($value  as $k => $v) {
				if( isset($data['anwser'][$v]) ) {
					unset($data['anwser'][$v]);
				}
			}
		}

		$num =$data['type'];


		$number = count($data['anwser']);
		$count = Db::table('question')->where('q_type',$num)->count();
		if($num == 2) {
			$count = $count-2;
		}
		if($count <= $number) {
			$mysql_data['status'] = 1;
		}
		if( isset($data['text']) ) {
			$mysql_data['u_desc'] = json_encode($data['text']);
		}
		$mysql_data['u_answer'] = json_encode($data['anwser']);
		$mysql_data['u_id'] = $u_id;
		$mysql_data['u_date'] = $data['date'];
		$mysql_data['type'] = $num;

		//查询用户是否有数据 有 执行修改操作 无执行添加操作
		$map['u_id'] = $u_id;
		$map['u_date'] = $data['date'];
		$map['type'] = $num;
		$info = Db::table('user_answer')->where($map)->find();
		if($info) {
			$res = Db::table('user_answer')->where($map)->update($mysql_data);
			if($res!==false) {
				$res = true;
			}
		} else {
			$res = Db::table('user_answer')->insert($mysql_data);
		}
		if($res) {
			$week = date('w', strtotime($data['date']));
			if($week == 0) {
				$num++;
				if($num<=3){
					$this->redirect('nutrientbalance/question', ['date'=>$data['date'],'num'=>$num]);
				}else{
					$this->redirect('index/nutrientbalance');
				}
			}else{
				$this->redirect('index/nutrientbalance');
			}
		} else {
			$this->error('失败');
		}
    }

	/**
	 * 获取当月答卷状态
	 * @param string $date 日期
	 */
	public function getcolor($date = 0) {

		if (!Request::instance()->isAjax()) {
			echo json_encode(array());die;
		}

		if( !Session::has('uid') ) {
			echo json_encode(array());die;
		}
		$u_id = Session::get('uid');

		if($date == 0 || strtotime($date) === false) {
			echo json_encode(array());die;
		}

		$firstday = date("Y-m-01", strtotime($date));
		$lastday = date("Y-m-d", strtotime("$firstday 1 month -1 day"));

		$res = Db::table('user_answer')
			->field('u_date,status')
			->where('u_date','between',[$firstday,$lastday])
			->where('u_id',$u_id)
			->select();

		$arr  = array();
		foreach ($res as $k => $v) {
			if(!isset($arr[date('j',strtotime($v['u_date']))])){
				$arr[date('j',strtotime($v['u_date']))] = $v['status'];
			}else{
				if($arr[date('j',strtotime($v['u_date']))]!=0){
					$arr[date('j',strtotime($v['u_date']))] = $v['status'];
				}
			}
		}
		echo json_encode($arr);
	}

	/**
	 * 获取两天之间的答卷数量
	 * @param string $s_time 开始日期
	 * @param string $e_time 结束日期
	 */
	public function getdays($s_time = 0, $e_time = 0) {

		if (!Request::instance()->isAjax()) {
			echo 0;die;
		}

		if(!Session::has('uid')) {
			echo 0;die;
		}
		$u_id = Session::get('uid');

		if($s_time == 0 || $e_time == 0 || strtotime($s_time) === false || strtotime($e_time) === false) {
			echo 0;die;
		}
		if($s_time > $e_time) {
			echo 0;die;
		}

		$res = Db::table('user_answer')
			->where('u_date', 'between', [$s_time,$e_time])
			->where('u_id', $u_id)
			->count();

		echo $res;
	}

	/**
	 * 生成营养处方
	 * @param Request $request 接收参数 开始时间 结束时间
	 */
	public function generateorder(Request $request) {

		if (!Request::instance()->isAjax()) {
			echo 0;die;
		}

		if( !Session::has('uid') ) {
			echo 0;die;
		}
		$u_id = Session::get('uid');

		$s_time = $request->post("s_time");
		$e_time = $request->post("e_time");
		if(strtotime($s_time) === false ||strtotime($e_time) === false) {
			echo 0;die;
		}

		$r = Db::table('order')
			->alias('a')
			->field('a.o_id')
			->join('online_report o','a.o_id = o.or_id')
			->where('a.status','1')
			->where('a.u_id',$u_id)
			->whereTime('o.effective_time', '>=', date("Y-m-d H:i:s",time()))
			->where('a.type','in',[3,4])
			->order('o.add_time DESC')
			->limit(1)
			->find();
		if(isset($r['o_id'])){
			$mysql_date['p_id'] = $r['o_id'];
		}else{
			echo 0;die;
		}

		$res = Db::table('user_answer')
			->field('u_date,u_answer,u_desc,type')
			->where('u_date', 'between', [$s_time,$e_time])
			->where('u_id', $u_id)
			->select();

		foreach ($res as $ke => $value) {
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
			$details[$value['type']][$value['u_date']] = Db::field('q.q_id,q.q_content,a.a_content,GROUP_CONCAT(a.a_content) as a_content')
				->table('question_answer')
				->alias('qa')
				->join('question q','qa.q_id = q.q_id')
				->join('answer a','qa.a_id = a.a_id')
				->where('qa.qa_id','in',"$str")
				->group('q.q_id')
				->select();


			//问答题
			if($value['u_desc'] != "") {
				$desc = json_decode($value['u_desc'], true);
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
				$descs[$value['type']][$value['u_date']] = $desc_question;
			}
		}

		$detail = array();
		if( !isset($details) ) {
			echo 4;die;
		}
		foreach ($details as $key => $value) {
			foreach ($value as $k => $v) {
				foreach ($v as $ke =>$val){
					$detail[$key][$k][$val['q_id']] = array(
						'question' 	=> $val['q_content'],
						'answer' 	=> $val['a_content'],
					);
				}

			}
		}
		if (isset($descs)) {
			foreach ($descs as $key => $value) {
				foreach ($value as $k => $v) {
					foreach ($v as $ke=>$val){
						if ($val['a_content'] != '') {
							$detail[$key][$k][$val['q_id']]['desc'] = $v['a_content'];
						}
					}
				}
			}
		}

		//营养TYPE 1
		$order_data['type'] = 1;
		$order_data['u_id'] = $u_id;
		$order_data['out_trade_no'] = Order::createuniquenumber();
		$order_data['addtime'] = date("Y-m-d H:i;s",time());
		$num = Db::name('order')->insertGetId($order_data);

		$mysql_date['desc'] = json_encode($detail);
		$mysql_date['o_id'] = $num;
		if(Db::name('nutrition_order')->insert($mysql_date)){
			echo $num;
		}else{
			echo 0;
		}

	}

	/**
	 * 获取营养处方价格 id为5
	 */
	public function getprice() {
		if (Request::instance()->isAjax()) {
			$price = Db::table("price_class")->field("p_price")->where('p_id', 5)->find();
			echo $price['p_price'];
		}
	}

	public function getagetest()
	{
		if( !Session::has('uid') ) {
			echo 0;die;
		}
		$u_id = Session::get('uid');
		$res = Db::table('order')
			->alias('a')
			->field('a.o_id')
			->join('online_report o','a.o_id = o.or_id')
			->where('a.status','1')
			->where('a.u_id',$u_id)
			->whereTime('o.effective_time', '>=', date("Y-m-d H:i:s",time()))
			->where('a.type','in',[3,4])
			->order('o.add_time DESC')
			->limit(1)
			->find();
		if(isset($res['o_id'])){
			echo 1;
		}else{
			echo 0;
		}
	}
}