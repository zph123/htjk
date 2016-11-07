<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Localetest;
use app\index\model\Online_report;
class Locale extends Common
{
    public function _initialize()
    {
        //初始化，以后设计用
    }

    //在线测试数据 已支付未生成测试报告的数据
    public function index()
    {
        $users=Request::instance()->get('user');
        $page=Request::instance()->get('page');
        $parameter=array();
        if(!empty($users))
        {
            $parameter['where']=$users;
            $where=array("n_name"=>$users);
        }
        else
        {
            $where=1;
        }
        $user = new Localetest();
        $arr = $user->two_select($where);
        $number=count($arr);
        $paging=5;
        $leaf=ceil($number/$paging);
        $page=isset($_GET['page'])?$_GET['page']:1;
        $start=($page-1)*$paging;
        $lastpage=$page-1<1?1:$page-1;
        $nextpage=$page+1>$leaf?$leaf:$page+1;
        $data = $user->on_select($where,$start);
        if(!empty($users))
        {
            $parameter=array("where"=>$users,"page"=>$page,"nextpage"=>$nextpage,"lastpage"=>$lastpage,"leaf"=>$leaf);
        }
        else
        {
            $parameter=array("page"=>$page,"nextpage"=>$nextpage,"lastpage"=>$lastpage,"leaf"=>$leaf);
        }

        $this->assign('page',$parameter);
        $this->assign('data',$data);
        return $this->fetch('index');
    }

    /*
    *生成测试报告数据
    */
    public function local_show(){
        $n_id=Request::instance()->get('n_id');
        $now = new Localetest();
        $arr=$now->one_select($n_id);
        $this->assign('data',$arr);
        return $this->fetch('local_show');
    }

    /*
    *测试报告数据入库
    *$arr 接受数据 
    */
    public function create()
    {
        $model = new  Localetest();
        $arr=Request::instance()->post();
        $arr['height']   = json_encode($arr['height']);
        $arr['weight']   = json_encode($arr['weight']);
        $arr['chest']    = json_encode($arr['chest']);
        $arr['gpbone']   = json_encode($arr['gpbone']);
        $arr['chnbone']  = json_encode($arr['chnbone']);
        $arr['ch05bone'] = json_encode($arr['ch05bone']);
        $arr['tw3c']     = json_encode($arr['tw3c']);
        $arr['tw3r']     = json_encode($arr['tw3r']);
        $arr['add_time'] = date('Y-m-d H:i:s',time());
        $model->update_nowtest($arr['n_id']);

        $Online_report = new Online_report();
        $res=$Online_report->report_add($arr);
        if($res)
        {
            $this->success('保存成功','Locale/index',3);
        }
        else
        {
            $this->error('保存失败','Locale/index',3);
        }
    }

    /*
    *已经生成测试报告的用户
    */
    public function nowtest_show(){
        $users=Request::instance()->get('user');
        $page=Request::instance()->get('page');
        $parameter=array();
        if(!empty($users))
        {
            $parameter['where']=$users;
            $where=array("n_name"=>$users);
        }
        else
        {
            $where=1;
        }
        $now = new Localetest();
        $arr = $now->now_select($where);
        $number=count($arr);
        $paging=5;
        $leaf=ceil($number/$paging);
        $page=isset($_GET['page'])?$_GET['page']:1;
        $start=($page-1)*$paging;
        $lastpage=$page-1<1?1:$page-1;
        $nextpage=$page+1>$leaf?$leaf:$page+1;
        $data = $now->now_test($where,$start);
        if(!empty($users))
        {
            $parameter=array("where"=>$users,"page"=>$page,"nextpage"=>$nextpage,"lastpage"=>$lastpage,"leaf"=>$leaf);
        }
        else
        {
            $parameter=array("page"=>$page,"nextpage"=>$nextpage,"lastpage"=>$lastpage,"leaf"=>$leaf);
        }
        $this->assign('page',$parameter);
        $this->assign('data',$data);
        return $this->fetch('now_test');
    }

    /*
    *现场测试报告具体数据展示
    */
    public function nowtest_look(){
        $n_id=Request::instance()->get('n_id');
        $where = "online_report.n_id=".$n_id;
        $model = new Online_report();
        $arr=$model->now_show($where);
        $arr[0]['height']   = json_decode($arr[0]['height'],true);
        $arr[0]['weight']   = json_decode($arr[0]['weight'],true);
        $arr[0]['chest']    = json_decode($arr[0]['chest'],true);
        $arr[0]['gpbone']   = json_decode($arr[0]['gpbone'],true);
        $arr[0]['chnbone']  = json_decode($arr[0]['chnbone'],true);
        $arr[0]['ch05bone'] = json_decode($arr[0]['ch05bone'],true);
        $arr[0]['tw3c']     = json_decode($arr[0]['tw3c'],true);
        $arr[0]['tw3r']     = json_decode($arr[0]['tw3r'],true);
        $this->assign('data',$arr[0]);
        return $this->fetch('nowtest_show');
        print_r($arr);
    }
}
