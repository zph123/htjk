<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Localetest;
use app\index\model\Online_report;
class Locale extends Common
{

    //在线测试数据 已支付未生成测试报告的数据
    public function index()
    {
        $out_trade_no = Request::instance()->get('out_trade_no');
        $is_pay=Request::instance()->get('is_pay');
        $status=Request::instance()->get('status');
        $page=Request::instance()->get('page');
        $name=Request::instance()->get('name');
        $id_number=Request::instance()->get('id_number');
        $parameter=array();
        $where=array();
        if(!empty($id_number)){
            $where['us.id_number']=$id_number;
        }        
        if(!empty($out_trade_no)){
            $where['out_trade_no']=$out_trade_no;
        }
        if(!empty($name)){
            $where['gl_users.name']=$name;
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
        $online = new Localetest();
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
        $this->assign('id_number',$id_number);
        $this->assign('order',$out_trade_no);
        $this->assign('page',$parameter);
        $this->assign('data',$data);
        return $this->fetch('index');
    }

    /*
    *生成测试报告数据
    */
    public function local_show(){
        $id = Request::instance()->get('o_id');
        $user = new Localetest();
        $data = $user->one_select($id);
        $data['n_age'] = $this->birthday($data['n_date']);
        $this->assign('data',$data);
        return $this->fetch('local_show');
    }

    //根据出生日期计算年龄
    function birthday($birthday){ 
     $age = strtotime($birthday); 
     if($age === false){ 
      return false; 
     } 
     list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age)); 
     $now = strtotime("now"); 
     list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now)); 
     $age = $y2 - $y1; 
     if((int)($m2.$d2) < (int)($m1.$d1)) 
      $age -= 1; 
     return $age; 
    } 
    /*
    *测试报告数据入库
    *$arr 接受数据 
    */
    public function create(Request $request)
    {
        $model = new  Localetest();
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
        $Online_report = new Online_report();
        $res=$Online_report->report_add($arr);
        if($res)
        {
            $model->order_update($arr['or_id']);
            $this->success('保存成功','Locale/index',3);
        }
        else
        {
            $this->error('保存失败','Locale/index',3);
        }
    }
    /*
    *现场测试报告具体数据展示
    */
    public function nowtest_look(){
        $n_id=Request::instance()->get('o_id');
        $where = "online_report.or_id=".$n_id;
        $model = new Online_report();
        $arr=$model->now_show($where);
        $arr[0]['n_age']=$this->birthday($arr[0]['n_date']);
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
