<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Cookie;
use think\Db;
use think\Request;
use app\index\model\user_model;

class Sportaction extends Common
{
	/**
	 * 加载运动处方首页
	 */
	function index(){
        //查询单次价格
        $price=Db::table('price_class')->where('p_id','4')->find();
        $this->assign('price',$price['p_price']);
		return view('index/sportAction');
	}
	/**
     * 获取用户运动处方数据
     */
    function getcolor(Request $request){
    	$data=$request->param();
        $date=empty($data['data'])?"":$data['data'];
        $id=Session::get('uid');
        $field='id,time';
        $model=new user_model();
        $data=$model->user_motion('user_motion',$id,$field,$date);
        $da=$this->words($data);
        exit(json_encode($da));
    }
    /**
     * 处理用户重复数据
     */
    function words($data){
    	$da=array();
        $arr=array();
    	foreach ($data as $key => $value) {
            $i=1;
            foreach ($data as $k => $v) {
                if($k!=$key){
                    if(empty($arr[$value])){
                        if($value==$v){
                            $i++;
                            $arr[$value]['num']=$i;
                        }
                    }
                }
            }
            if(empty($arr[$value])){
                $arr[$value]['num']=$i;
            }
        }
        foreach ($arr as $key => $value) {
            $k=date('j',strtotime($key));
            $da[$k]=$value['num'];
        }
        return $da;
    }
    /**
     * 运动数据列表
     */
    function question(Request $request){
        $data=$request->param();   
        if(strtotime($data['data'])!==FALSE){
            $patten = "/^[0-9]{4}-((([13578]|(10|12))-([1-9]|[1-2][0-9]|3[0-1]))|(2-([1-9]|[1-2][0-9]))|(([469]|11)-([1-9]|[1-2][0-9]|30)))$/";
            if(preg_match($patten,$data['data'])){
                $date=$data['data'];
                $time=strtotime($date);
                if($time>time()){
                    $this->error('未来还没到哦~');
                }else{
                    $this->assign('time',$date);
                    return view();
                }
            }
        }        
    }
    /**
     * 详情页
     */
    function userContent(Request $request){
        $uid=Session::get('uid');
        if(isset($uid)){
            $data=$request->get();
            if(isset($data['r'])){
                $r=$data['r'];
                $model=new user_model();
                $data=$model->user_find('user_motion',$r,$uid);
                $this->assign('list', $data);
                return $this->fetch('usercontent');
            }
        }        
    }
    /**
     * 用户添加过的列表
     */
    function userList(Request $request){
        $id=session::get('uid');
        if(isset($id)){
            $field="id,time,m_content,m_time";
            $model=new user_model();
            $data=$model->user_select('user_motion',$id,$field);
            $this->assign('list',$data);
            return view();
        }
    }
    /**
     * 添加数据进入后台
     */
    function motionAdd(Request $request){
        $id=session::get('uid');
        if(isset($id)){
            $data=$request->post();
            if(count($data)>13){
               $num=$data['num'];
               if(isset($num)){
                    $array=array(
                        'm_time'=>$data['time'],
                        'time'=>$data['date'],
                        'm_content'=>$data['content'],
                        'm_continued'=>$data['continued'][$num],
                        'feel'=>$data['feel'],
                        'm_feel'=>$data['m_feel'],
                        'emotion'=>$data['emotion'],
                        'face'=>$data['face'],
                        'sleep'=>$data['sleep'],
                        'eat'=>$data['eat'],
                        'perspiration'=>$data['perspiration'],
                        'breathing'=>$data['breathing'],
                        'weight'=>$data['weight'],
                        'lung'=>$data['lung'],
                        'blood'=>$data['blood'],
                        'uid'=>$id
                    );
                    $re=Db::table('user_motion')->insert($array);
                    if($re){
                        $this->redirect('sportaction/index');
                    }else{
                        $this->error('添加失败啦~');
                    }
               }
            }
        }
    }
    /**
     * 用户选择要生成的时间段
     */
    function generateorder(Request $request){
        $id=Session::get('uid');
        $data=$request->post();
        $s_time=$data['s'];
        $e_time=$data['e'];
        if(strtotime($s_time)!==FALSE&&strtotime($e_time)!==FALSE){
            $patten = "/^(19|20)\d{2}-(0?\d|1[012])-(0?\d|[12]\d|3[01])$/";
            if(!preg_match($patten,$s_time)){
                $error['error']='0';
                $error['content']='开始时间的格式不正确，格式为2011-11-11';
                exit(json_encode($error));
            }else if(!preg_match($patten,$e_time)){
                $error['error']='0';
                $error['content']='结束时间的格式不正确，格式为2011-11-11';
                exit(json_encode($error));
            }
        } 
        $snum=strtotime($s_time);
        $enum=strtotime($e_time);
        $i=$snum;
        while ($i<$enum) {
            $array[]=$s_time;
            //找到开始时间的那周周日
            $ssday=$this->getSunday($s_time);
            if(strtotime($ssday)>=$enum){
                $array[]=$e_time;
            }else{
                $array[]=$ssday;
            }
            $s_time=$this->getLastMonday($ssday);
            $i=strtotime($ssday);
        }
        $count=count($array)/2;
        if($count<12){
            $error['error']='0';
            $error['content']='提交的时间少于12周';
            exit(json_encode($error));
        }
        $field='time,id';
        for($i=0;$i<count($array);$i++){
            $s=$array[$i];
            $i++;
            $e=$array[$i];
            $temp=Db::table("user_motion")
            ->where('uid',$id)
            ->where('time','between',[$s,$e])
            ->column($field);
            if(count($temp)<2){
                $error['error']='0';
                $error['content']=$s.'到'.$e.'之间数据不完整';
                exit(json_encode($error));
            }
        }
        //生成订单
        $number = "YY".date("YmdHis",time()).rand(10000,99999);
        //拼接数据
        $array=array(
            'u_id'=>$id,
            'm_number'=>$number,
            'add_time'=>date('Y-m-d'),
            'begin_time'=>$data['s'],
            'end_time'=>$data['e']
            );
        $model=new user_model();
        $re=$model->add_motion('motion_order',$array);
        if($re){
            $error['error']='1';
            $error['content']='生成订单成功是否去支付';
        }else{
            $error['error']='0';
            $error['content']='出现故障啦，请刷新试试';
        }
        exit(json_encode($error));
    }
    /**
     * 查询用户是否填写过运动处方
     */
    function user_write($date){
    	$id=Session::get('uid');
    	$data = Db::table("user_motion")
		->where('uid',$id)
		->order('time desc')
		->column('id,time');
		// 用户还未提交过数据直接返回
		if(count($data)<=0){
			return 'true';
		}
		//去除同一天的提交量
		foreach ($data as $key => $value) {
			$datat[$value]=$value;
		}
		//将数据位置固定
		foreach ($datat as $key => $value) {
			$datatime[]=$value;
		}
		//获取最近提交一次的周日
		$near=$this->getMonday($datatime[0]);
		//当前时间是属于那一周
		$nowclass=$this->getMonday($date);
		$nowend=$this->getSunday($date);
		$numnow=strtotime($nowclass);
		$numend=strtotime($nowend);
		// 
		$nuntime=strtotime($datatime[0]);
		//判断提交过之后是否是本周的
		// var_dump($nuntime);die;
		if($nuntime>=$numnow&&$nuntime<=$numend){
			return 'true';
		}else if(count($data)<=1){//不是本周的如果只有一条请补齐
			return 'false';
		}else if(strtotime($near)>strtotime($datatime[1])){//不是本周的如果有两条是否是同一周的
			return 'false';
		}else{
			return 'true';
		}
    }
    /**
     * 获取下一个周一
     */
    function getLastMonday($d){
        return date('Y-m-d' , strtotime('next monday' , strtotime($d)));
    }
    /**
     * 获取上个一周一
     */
    function getMonday($d){
	    if (date('D',strtotime($d))=='Mon'){
	        return date('Y-m-d',strtotime($d));
	    }else {
	        return date('Y-m-d' , strtotime('last monday' , strtotime($d)));
	    }
	}
    /**
     * 获取下个一周日
     */
    function getSunday($d){
	    if (date('D',strtotime($d))=='Sun'){
	        return date('Y-m-d',strtotime($d));
	    }else {
	        return date('Y-m-d' , strtotime('next sunday' , strtotime($d)));
	    }
	}

}