<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Cookie;

class Index extends Controller
{
	public function _initialize()
    {
        //初始化，以后设计用
    }
    public function index()
    {
        $arr=Db::table('introduce')->field('id,img_path')->select();
        $cat=Db::table('category_article')->select();
        $banner=Db::table('banner')->field('b_id,banner_path')->select();
        $this->assign('arr',$arr);
        $this->assign('cat',$cat);
        $this->assign('banner',$banner);
        return $this->fetch('index');        
    }

    public function indexshow(Request $request){
         $id=Request::instance()->get('id');
         $where=array("id"=>$id);
         $show=Db::table('introduce')->where($where)->field('content')->select();
         $this->assign('show',$show);
         return $this->fetch('whatBone');         
    }

    public function articlelist(Request $request){
         $c_id=Request::instance()->get('c_id');
         $where=array("c_id"=>$c_id);
         $show=Db::table('article')->where($where)->select();
         $this->assign('show',$show);
         return $this->fetch('articlelist'); 
         // print_r($show);       
    }

    public function bannerlist(){
         $id=Request::instance()->get('b_id');
         $where=array("b_id"=>$id);
         $show=Db::table('banner')->where($where)->field('contents')->select();
         $this->assign('show',$show);
         return $this->fetch('bannerlist');         
    }

    public function nowTest()
    {
        return view('nowTest');
    }
    public function onlineTest()
    {
        return view('onlineTest');
    }
    // public function sportAction(){
    //  return view('sportAction');
    // }
    public function nutrientBalance(){
    	return view('nutrientBalance');
    }
    public function predictedHeight(){
    	//暂无页面，临时跳转到index
    	return view('index');
    }
    public function userCenter(){
        $id=Cookie::get('uid');
        if(empty($id)){
            $this->assign('name','false');
            $this->assign('out','false');//这里为前台是否显示退出标示
        }else{
            $user=Db::table('gl_users')->where('id',$id)->find();
            Cookie::set('username', $user['name']);
            $this->assign('name',$user['name']);
            $this->assign('out','true');//这里为前台是否显示退出标示
        }
        return view('userCenter');
    }
    
/**
     * 什么是骨龄测试
     */
    public function whatBone()
    {
       return view('whatBone'); 
    }
    /**
     * 什么年龄做骨龄测试
     */
    public function whatAge()
    {
       return view('whatAge'); 
    }/**
     * 为什么做骨龄测试
     */
    public function whyBone()
    {
       return view('whyBone'); 
    }/**
     * 怎么做骨龄测试
     */
    public function howBone()
    {
       return view('howBone'); 
    }

}
