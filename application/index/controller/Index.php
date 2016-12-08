<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;

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
        $this->assign('arr',$arr);
        $this->assign('cat',$cat);
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

    public function nowTest()
    {
        return view('nowTest');
    }
    public function onlineTest()
    {
        return view('onlineTest');
    }
    public function sportAction(){
    	return view('sportAction');
    }
    public function nutrientBalance(){
    	return view('nutrientBalance');
    }
    public function predictedHeight(){
    	//暂无页面，临时跳转到index
    	return view('index');
    }
    public function userCenter(){
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
