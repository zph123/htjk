<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Cookie;
use app\index\model\Below_list;

class Activity extends Common
{
    //首页
    public function index()
    {
        $user = new Below_list();
        $arr=$user->activity_select();
        return view('index',['arr'=>$arr]);
    }

    //活动添加页面
    public function activity_add()
    {
        return view('activity_add');
    }
    //活动添加方法
    public function activity_insert()
    {
        $l_place = Request::instance()->post('l_place');
        $l_stime = Request::instance()->post('l_stime');
        $l_etime = Request::instance()->post('l_etime');

        $l_stime=$this->time($l_stime);
        $l_etime=$this->time($l_etime);

        $data=array(

            'l_place'=>$l_place,
            'l_stime'=>$l_stime,
            'l_etime'=>$l_etime
        );
        $user = new Below_list();
        $arr=$user->activity_add($data);
        if($arr){
            echo 1;
        }else{
            echo 2;
        }

    }
    function time($time){

        $time=explode(' ',$time);
        $time1=explode('/',$time[0]);
        return $time1[2].'-'.$time1[0].'-'.$time1[1].' '.$time[1].':00';

    }
    //活动删除
    function activity_delete(){

        $l_id = Request::instance()->post('l_id');
        $user = new Below_list();
        $arr=$user->activity_del($l_id);
        if($arr){
            echo 1;
        } else{

            echo 2;
        }
    }

}
