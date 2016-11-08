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
        $field='o_id,status,is_pay,out_trade_no,addtime';
        $model=new user_model();
        $data=$model->user_order($id,$type,$field);
        foreach($data as $key=>$value){
            if($value['is_pay']==0){
                $data[$key]['is_pay']='未支付';
            }else{
                $data[$key]['is_pay']='已支付';
            }
            if($value['status']==0){
                $data[$key]['status']='未生成';
            }else{
                $data[$key]['status']='已生成';
            }
        }
        $this->assign('list', $data);
    	return $this->fetch();
    }
    /**
     * 测试报告
     */
    function report(){
        $id=Session::get('uid');
        $type='3';
        $field='o_id,status,is_pay,out_trade_no,addtime';
        $model=new user_model();
        $data=$model->user_order($id,$type,$field);
        foreach($data as $key=>$value){
            if($value['is_pay']==0){
                $data[$key]['is_pay']='未支付';
            }else{
                $data[$key]['is_pay']='已支付';
            }
            if($value['status']==0){
                $data[$key]['status']='未生成';
            }else{
                $data[$key]['status']='已生成';
            }
        }
        $this->assign('list', $data);
        return $this->fetch('nutrition');
    }
    /**
     *运动处方
     */
    function motion(){
        $id=Session::get('uid');
        $type='2';
        $field='o_id,status,is_pay,out_trade_no,addtime';
        $model=new user_model();
        $data=$model->user_order($id,$type,$field);
        foreach($data as $key=>$value){
            if($value['is_pay']==0){
                $data[$key]['is_pay']='未支付';
            }else{
                $data[$key]['is_pay']='已支付';
            }
            if($value['status']==0){
                $data[$key]['status']='未生成';
            }else{
                $data[$key]['status']='已生成';
            }
        }
        $this->assign('list', $data);
        return $this->fetch('nutrition');
    }
    /**
     * 查看详情
     */
    function see(Request $request){
        $uid=session::get('uid');
        if(isset($uid)){
            $data=$request->param();
            $id=empty($data['r'])?"":$data['r'];
            // $model=new user_model();
            // $data=$model->user_find(,$id,$uid);
            // $this->assign('list', $data);
            return $this->fetch('content');
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