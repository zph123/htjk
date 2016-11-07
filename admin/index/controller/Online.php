<?php
namespace app\index\controller;
use think\console\Command;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Onlinetest;
use app\index\model\Online_report;

class Online extends Common
{
    //在线测试数据 已支付未生成测试报告的数据
    public function index()
    {
        $users=Request::instance()->get('user');
        $page=Request::instance()->get('page');
        $parameter=array();
        if(!empty($users))
        {
            $parameter['where']=$users;
            $where=array("customer"=>$users);
        }
        else
        {
            $where=1;
        }
        $user = new Onlinetest();
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
    /**
     * 在线测试数据详情
     * @return [type] [description]
     */
    public function show()
    {

        $id = Request::instance()->get('id');
        $user = new Onlinetest();
        $data = $user->one_select($id);
        $this->assign('data',$data);
        return $this->fetch('show');
    }

    //测试报告页面
    public function report()
    {
        $id = Request::instance()->get('id');
        $user = new Onlinetest();
        $data = $user->one_select($id);
        $this->assign('data',$data);
        return $this->fetch('report');
    }

    /*
    *生成在线测试报告
    *$arr 接受数据 
    */
    public function create()
    {
        $model = new Online_report();
        $onl  = new Onlinetest();
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
        $onl->updates($arr['or_id']);
        $res=$model->report_add($arr);
        if($res)
        {
            $this->success('保存成功','Online/index',3);
        }
        else
        {
            $this->error('保存失败','Online/index',3);
        }
    }

    /*
    *已经生成测试报告的用户
    */
    public function report_show(){
        $users=Request::instance()->get('user');
        $page=Request::instance()->get('page');
        $parameter=array();
        if(!empty($users))
        {
            $parameter['where']=$users;
            $where=array("customer"=>$users);
        }
        else
        {
            $where=1;
        }
        $user = new Onlinetest();
        $arr = $user->tr_select($where);
        $number=count($arr);
        $paging=5;
        $leaf=ceil($number/$paging);
        $page=isset($_GET['page'])?$_GET['page']:1;
        $start=($page-1)*$paging;
        $lastpage=$page-1<1?1:$page-1;
        $nextpage=$page+1>$leaf?$leaf:$page+1;
        $data = $user->onl_test($where,$start);
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
        return $this->fetch('test');
    }

    /*
    *在线测试报告具体展示
    */
    public function test_show(){
        $reg_id=Request::instance()->get('reg_id');
        $where = "onlinetest.register_id=".$reg_id;
        $model = new Online_report();
        $arr=$model->report_show($where);
        $arr[0]['height']   = json_decode($arr[0]['height'],true);
        $arr[0]['weight']   = json_decode($arr[0]['weight'],true);
        $arr[0]['chest']    = json_decode($arr[0]['chest'],true);
        $arr[0]['gpbone']   = json_decode($arr[0]['gpbone'],true);
        $arr[0]['chnbone']  = json_decode($arr[0]['chnbone'],true);
        $arr[0]['ch05bone'] = json_decode($arr[0]['ch05bone'],true);
        $arr[0]['tw3c']     = json_decode($arr[0]['tw3c'],true);
        $arr[0]['tw3r']     = json_decode($arr[0]['tw3r'],true);
        $this->assign('data',$arr[0]);
        return $this->fetch('test_show');
    }
}