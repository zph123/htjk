<?php
namespace app\index\controller;
use think\Session;
use think\Cookie;
use think\Db;
use think\Request;
use app\index\model\user_model;

class User extends Common
{
    public function index()
    {
        $id=Session::get('uid');
        $user=Db::table('gl_users')->where('id',$id)->find();
        $this->assign('name',$user['name']);
        return view('index/userCenter');
    }
    /**
     * 加载营养均衡首页
     */
    function nutrition(){
        $id=Session::get('uid');
        $type='1';
        $field='o_id,out_trade_no,addtime';
        $model=new user_model();
        $data=$model->user_order($id,$type,$field);
        $this->assign('list', $data);
        $this->assign('name','营养均衡');
        return $this->fetch();
    }
    /**
     * 测试报告
     */
    function report(){
        $id=Session::get('uid');
        $field='o_id,out_trade_no,addtime';
        $data=Db::table('order')
        ->where('u_id',$id)
        ->where('type','in','3,4')
        ->order('addtime DESC')
        ->column($field);
        $this->assign('list', $data);
        $this->assign('name','测试报告');
        return $this->fetch('nutrition');
    }
    /**
     *运动处方
     */
    function motion(){
        $id=Session::get('uid');
        $type='2';
        $field='o_id,out_trade_no,addtime';
        $model=new user_model();
        $data=$model->user_order($id,$type,$field);
        $this->assign('list', $data);
        $this->assign('name','运动处方');
        return $this->fetch('nutrition');
    }
    /**
     * 查看详情
     */
    function see(Request $request){
        $uid=session::get('uid');
        if(isset($uid)){
            $arr=$request->param();
            $id=empty($arr['r'])?"":$arr['r'];
            $data=Db::table('order')
            ->where('u_id',$uid)
            ->where('o_id',$id)
            ->find();
            $is_pay=$data['is_pay'];
            if($data){
                if($data['is_pay']==0){
                    
                    $data['is_pay']='未支付';
                }else{
                    $data['is_pay']='已支付';
                }
                if($data['status']==0){
                    $data['status']='未生成';
                }else{
                    $data['status']='已生成';
                }
            }
            if($data['type']==1){
                $way=Db::table('motion_way')->where('u_id',$uid)->where('o_id',$id)->find();
            }else if($data['type']==2){
                $way=Db::table('motion_way')->where('u_id',$uid)->where('o_id',$id)->find();
            }else if($data['type']==3){
                $way=Db::table('motion_way')->where('u_id',$uid)->where('o_id',$id)->find();
            }else if($data['type']==4){
                $way=Db::table('motion_way')->where('u_id',$uid)->where('o_id',$id)->find();
            }
            $this->assign('way',$way);
            $this->assign('is_pay',$is_pay);
            $this->assign('list', $data);
            return $this->fetch('content');
        }        
    }
    /**
     * 去支付
     */
    function pay(Request $request){
        $uid=session::get('uid');
        $arr=$request->param();
        $id=empty($arr['r'])?"":$arr['r'];
        $data=Db::table('order')
        ->where('u_id',$uid)
        ->where('o_id',$id)
        ->find();
        if($data){
            if($data['is_pay']==0){
                Cookie::set('out_trade_no', $data['out_trade_no']);
                $this->redirect("http://www.zphteach.com/htjk/WxpayAPI_php_v3/example/jsapi.php");
                //$this->redirect("http://www.zphteach.com/htjk/WxpayAPI_php_v3/example/jsapi.php?trade=$data[out_trade_no]");
                // var_dump($data);
            }else{
                  echo 'is_pay  1'; 
            }            
        }else{
            echo 'error';
        }
        
    }
    /**
     *删除订单
     */
    function user_delete(Request $request){
        $uid=session::get('uid');
        $arr=$request->param();
        $id=empty($arr['r'])?"":$arr['r'];
        $data=Db::table('order')
        ->where('u_id',$uid)
        ->where('o_id',$id)
        ->find();
        if($data){
            $re=Db::table('order')->where('u_id',$uid)->where('o_id',$id)->delete();
            if($re){
                if($data['type']==1){
                    $action='nutrition';
                    $type=Db::table('nutrition_order')->where('o_id',$id)->delete();
                }else if($data['type']==2){
                    $action='motion';
                    $type=Db::table('motion_order')->where('order_id',$id)->delete();
                }else if($data['type']==3){
                    $action='report';
                    $type=Db::table('nowtest')->where('uid',$uid)->where('o_id',$id)->delete();
                }else if($data['type']==4){
                    $action='report';
                    $type=Db::table('onlinetest')->where('uid',$uid)->where('o_id',$id)->delete();
                }
                $this->redirect("$action");
            }else{
                $this->error('订单删除失败');
            }
        }else{
            $this->error('该订单不存在');
        }
    }
    /**
     * 退出登录
     */
    function quit(){
        Cookie::set('uid',null);
        Session::set('uid',null);
        $this->redirect('index/index');
    }
}