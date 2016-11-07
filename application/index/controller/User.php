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
        $where=Session::get('uid');
        $field='title,time,id,content';
        $model=new user_model();
        $data=$model->user_select('content',$where,$field);
        $this->assign('list', $data);
    	return $this->fetch();
    }
    /**
     * 测试报告
     */
    function report(){
        $where=Session::get('uid');
        $field='title,time,id,content';
        $model=new user_model();
        $data=$model->user_select('content',$where,$field);
        $this->assign('list', $data);
        return $this->fetch('nutrition');
    }
    /**
     *运动处方
     */
    function motion(){
        $where=Session::get('uid');
        $field='id,title,time,content';
        $model=new user_model();
        $data=$model->user_select('content',$where,$field);
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
            $model=new user_model();
            $data=$model->user_find('content',$id,$uid);
            $this->assign('list', $data);
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