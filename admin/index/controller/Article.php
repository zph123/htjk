<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
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

    /**文章分类页面  作者：李斌
     * @return mixed
     */
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

    //文章分类即点及改  作者：李斌
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
    //文章分类添加页面  作者：李斌
    public function category_a_add(){
        return view('category_add');

    }
    //文章分类添加  作者：李斌
    public function category_add(){
        $data['c_name']=input('c_name');
        $obj_model = new ArticleModel();
        $res=$obj_model->c_add_data($data);
        if($res)echo 1;
        else echo 0;
    }
    //检定分类下是否存在文章，如果没有直接删除  作者：李斌
    public function ajax_articles_check_a(){
        $c_id=input('c_id');
        $obj_model = new ArticleModel();
        $res=$obj_model->check_articles($c_id);
        if($res)echo 1;
        else{
            $res2=$obj_model->delete_category($c_id);
            if($res2)echo 2;
            else echo 0;
        }
    }
    //文章分类下有文章，先删除文章，在删除分类  作者：李斌
    public function ajax_category_delete(){
        $c_id=input('c_id');
        $obj_model = new ArticleModel();
        $res=$obj_model->delete_c_articles($c_id);
        if($res){
            $res=$obj_model->delete_category($c_id);
            if($res)echo 1;
            else echo 0;
        }
        else echo 0;
    }





    //刘锦龙
    //文章展示列表
    public function listing()
    {
        $c_id = Request::instance()->get('c_id');
        if(empty($c_id) || $c_id=='0'){
            $data=Db::table('article')
                ->alias('a')
                ->join('category_article c','a.c_id=c.c_id')
                ->select();
            $this->assign('c_id','0');
            $this->assign('data',$data);
        }else{
            $data=Db::table('article')
                ->alias('a')
                ->join('category_article c','a.c_id=c.c_id')
                ->where('a.c_id',$c_id)
                ->select();
            $this->assign('c_id',$c_id);
            $this->assign('data',$data);
        }

        $obj_model = new ArticleModel();
        $arr=$obj_model->count_category();
        $this->assign('arr',$arr);
        return view('article_list');
    }
    //文章添加列表
    public function article_add()
    {
        $obj_model = new ArticleModel();
        $arr=$obj_model->count_category();
        $this->assign('arr',$arr);
        return view('article_add');
    }
    //ajax添加
    public function article_pots()
    {
        $data['title'] = Request::instance()->post('title');
        $data['url'] = Request::instance()->post('url');
        $data['c_id'] = Request::instance()->post('c_id');
        $image = $_FILES['image'];
        $imgname = rand(1000,9999).time().$image['name'];
        $pathname = ROOT_PATH . 'public' . DS . 'article/'.$imgname;
        move_uploaded_file($image['tmp_name'],$pathname);
        $data['img'] = $imgname;
        $data['createtime'] = date('Y-m-d',time());
        $str=Db::table('article')->insert($data);
        if($str){
            $this->success('添加成功！','Article/article_add');
        }
    }
    //删除文章
    public function article_delete()
    {
        $article_id = Request::instance()->post('article_id');
        $str=Db::table('article')
            ->delete($article_id);
        if($str=='0'){
            echo 0;
        }else{
            echo 1;
        }

    }
}