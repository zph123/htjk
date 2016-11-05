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
    //在线测试数据
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
        $arr=Request::instance()->post();
        $arr['height'] = json_encode($arr['height']);
        $arr['weight'] = json_encode($arr['weight']);
        $arr['chest']   = json_encode($arr['chest']);
        $arr['gpbone'] = json_encode($arr['gpbone']);
        $arr['chnbone']= json_encode($arr['chnbone']);
        $arr['ch05bone'] =json_encode($arr['ch05bone']);
        $arr['tw3c'] = json_encode($arr['tw3c']);
        $arr['tw3r'] = json_encode($arr['tw3r']);
        $arr['add_time'] = date('Y-m-d H:i:s',time());
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
}