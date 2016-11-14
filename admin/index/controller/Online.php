<?php
namespace app\index\controller;
use think\console\Command;
use think\Controller;
use think\Config;
use think\Db;
use think\Request;
use app\index\model\Onlinetest;
use app\index\model\Online_report;

class Online extends Common
{
    //在线测订单
    public function index()
    {
        $out_trade_no = Request::instance()->get('out_trade_no');
        $is_pay=Request::instance()->get('is_pay');
        $status=Request::instance()->get('status');
        $name=Request::instance()->get('name');
        $page=Request::instance()->get('page');
        $parameter=array();
        $where=array();
        if(!empty($out_trade_no)){
            $where['out_trade_no']=$out_trade_no;
        }
        if(isset($is_pay) && $is_pay!=="" ){
            $where['is_pay']=$is_pay;
        }else{
            $is_pay="";
        }
        if(isset($status) && $status!==""){
            $where['status']=$status;
        }else{
            $status="";
        }
        if(!empty($name)){
            $where['gl_users.name']=$name;
        }         
        $online = new Onlinetest();
        $arr = $online->count_order($where);
        $number=count($arr);
        $paging=5;
        $leaf=ceil($number/$paging);
        $page=isset($_GET['page'])?$_GET['page']:1;
        $start=($page-1)*$paging;
        $lastpage=$page-1<1?1:$page-1;
        $nextpage=$page+1>$leaf?$leaf:$page+1;
        $data = $online->online_search($where,$start);

        $parameter['page']    =$page;
        $parameter['nextpage']=$nextpage;
        $parameter['lastpage']=$lastpage;
        $parameter['leaf']    = $leaf;
        $this->assign('name',$name);
        $this->assign('is_pay',$is_pay);
        $this->assign('status',$status);
        $this->assign('order',$out_trade_no);
        $this->assign('page',$parameter);
        $this->assign('data',$data);
        return $this->fetch('index');

    }

    //生成测试报告页面
    public function report()
    {
        $id = Request::instance()->get('o_id');
        $user = new Onlinetest();
        $data = $user->one_select($id);
        $this->assign('data',$data);
        return $this->fetch('show');
    }
    /*
    *生成在线测试报告
    *$arr 接受数据 
    */
    public function create(Request $request)
    {
        $model = new Online_report();
        $update = new Onlinetest();
        $arr=Request::instance()->post();
        $img=$request->file('hands_photo');
        if(!empty($img)){
            $Catalog_path=ROOT_PATH."public".DS."testimg";
            is_dir($Catalog_path)or mkdir($Catalog_path,0777,true);
            //将图片转移到 框架应用根目录/public/testimg/ 目录下
            $file_info = $img->move($Catalog_path);
            if ($file_info) {
                $imgname=$file_info->getSaveName();
            } else {
                // 上传失败获取错误信息，并显示
                $this->error($img->getError());
            }
        }else{
            $imgname="";
        }        
        $arr['height']   = json_encode($arr['height']);
        $arr['weight']   = json_encode($arr['weight']);
        $arr['chest']    = json_encode($arr['chest']);
        $arr['gpbone']   = json_encode($arr['gpbone']);
        $arr['chnbone']  = json_encode($arr['chnbone']);
        $arr['ch05bone'] = json_encode($arr['ch05bone']);
        $arr['tw3c']     = json_encode($arr['tw3c']);
        $arr['tw3r']     = json_encode($arr['tw3r']);
        $arr['add_time'] = date('Y-m-d H:i:s',time());
        $arr['test_path']= $imgname;
        $arr['effective_time'] = date('Y-m-d H:i:s',time()+3600*24*$arr['effective_time']);
        $res=$model->report_add($arr);
        if($res)
        {
            $update->order_update($arr['or_id']);
            $this->success('保存成功','Online/index',3);
        }
        else
        {
            $this->error('保存失败','Online/index',3);
        }
    }

    /*
    *在线测试报告具体展示
    */
    public function test_show(){
        $o_id=Request::instance()->get('o_id');
        $find=new Onlinetest();
        $res=$find->one_select($o_id);
        $where = "onlinetest.register_id=".$res['register_id'];
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

    public function Picture_download()
    {
        //获取图片的路径
        $img_path=$_GET['img_path'];
        $img=ROOT_PATH."public".DS."customer_uploads".DS.$img_path;
        //echo $tu;die;
        //获取图片的详细信息是一个数组
        $lie=getimagesize($img);
        //在数组中获取图片的类型
        $type=$lie['mime'];
        header('content-type:'.$type);
        //激活下载窗口
        header("Content-Disposition:attachment;filename=".$img);
        readfile($img);        
    }
}