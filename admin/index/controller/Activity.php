<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Cookie;
use app\index\model\Below_list;
header('content-type:text/html;charset=utf-8');
class Activity extends Common
{
    //首页
    public function index()
    {
        $user = new Below_list();
        $arr=$user->activity_select();
        $date= strtotime(date('Y-m-d H:i:s'));

        return view('index',['arr'=>$arr,'date'=>$date]);
    }

    //活动添加页面
    public function activity_add()
    {
        return view('activity_add');
    }
    //活动添加方法
    public function activity_insert()
    {
        $data['l_place']= Request::instance()->post('l_place');
        $l_stime = Request::instance()->post('l_stime');
        $l_etime = Request::instance()->post('l_etime');
        $data['l_notice'] = Request::instance()->post('l_notice');
        $data['l_astrict'] = Request::instance()->post('l_astrict');
        $data['list_astrict'] = Request::instance()->post('list_astrict');
        $data['l_stime']=$this->time($l_stime);
        $data['l_etime']=$this->time($l_etime);
        $data['l_status']=0;
        $user = new Below_list();
        $arr=$user->activity_add($data);
        if($arr){
            echo '1';
        }else{
            echo '0';
        }
    }
    function time($time)
    {
        $time=explode(' ',$time);
        $time1=explode('/',$time[0]);
        return $time1[2].'-'.$time1[0].'-'.$time1[1].' '.$time[1].':00';
    }
    //活动删除
    function activity_delete()
    {
        $l_id = Request::instance()->post('l_id');

        $user = new Below_list();

        $arr=$user->activity_get($l_id);
        if($arr[0]['l_apply']>'0'&&$arr[0]['l_status']=='1'){
            echo 0;
        }else{
            $str=$user->activity_del($l_id);
            if($str){
                echo 1;
            } else{

                echo 2;
            }
        }
    }
    function activity_dispose()
    {
        $l_id = Request::instance()->get('l_id');
        $dispose = Request::instance()->get('dispose');
        $user = new Below_list();
        if($dispose=='1'){
            $str=$user->activity_save($l_id,$dispose);
        }elseif($dispose=='2'){
            $str=$user->activity_save($l_id,$dispose);
        }

        if($str){
            $this->redirect('Activity/index');
        }else{
            $this->redirect('Activity/index');
        }
    }
    function activity_apply()
    {
        $page=Request::instance()->get('page');
        $l_id = Request::instance()->get('l_id');
        $user = new Below_list();
        $data1=$user->activity_activity($l_id);
        $data2=$user->activity_nowtest($l_id);
        $num['one']=count($data1);
        $num['two']=count($data2);
        $data=array_merge($data1,$data2);
        $this->assign('data',$data);
        $this->assign('num',$num);
        return view('activity_apply');
    }
}
