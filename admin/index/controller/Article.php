<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use app\index\model\Article as ArticleModel;

class Article extends Common
{
    /**
     * 1.文章分类页面----页
     *  1.1增删改查（即点及改）
     * 2.文章列表----页
     *  2.1删
     *  2.2增改查（即点及改）----页
     */

    //文章分类页面
    public function category_article(){

//        $page=Request::instance()->get('page');
        $obj_model = new ArticleModel();
//        $lines_page=8;
//        $total=$obj_model->count_category();
//        $total_number=count($total);
//
//        $leaf=ceil($total_number/$lines_page);
//        $page=isset($_GET['page'])?$_GET['page']:1;
//        $start=($page-1)*5;
//        $lastpage=$page-1<1?1:$page-1;
//        $nextpage=$page+1>$leaf?$leaf:$page+1;
//        $data = $obj_model->online_search($where,$start);
//
//
//        $arr = $obj_model->category_list($lines_page,);
//        $number=count($arr);
//
//        $where['lines_page']=$lines_page;
//
//
//
//        $parameter['page']    =$page;
//        $parameter['nextpage']=$nextpage;
//        $parameter['lastpage']=$lastpage;
//        $parameter['leaf']    = $leaf;
//
//        $this->assign('page',$parameter);
        $data=$obj_model->count_category();

        $this->assign('data',$data);
        return $this->fetch('category_article');
    }
    //文章列表页面
    public function articles_list(){
        return view('');
    }
    //文章编辑页面
    public function article(){
        return view('');
    }
    //文章分类即点及改
    public function ajax_category_a_data(){
        $data['c_id']=input('c_id');
        $data['c_name']=input('c_name');
        $obj_model = new ArticleModel();
        $str=$obj_model->category_update($data);
        if($str!=0){
            echo '1';
        }else{
            echo '0';
        }
    }
    //文章分类添加页面
    public function category_a_add(){
        return view('category_add');

    }
    //文章分类添加
    public function category_add(){
        $data['c_name']=input('c_name');
        $obj_model = new ArticleModel();
        $res=$obj_model->save($data);
        var_dump($res);
    }
    //文章编辑
    public function article_save(){
        $data=input();
        $obj_model = new ArticleModel();
        $obj_model->article_save($data);
    }
}