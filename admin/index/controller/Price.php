<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\Price_class;
use think\Db;

class Price extends Common
{
    //首页
    public function index()
    {
        return view('index');
    }
    //修改数据
    public function save()
    {
        $p_price=$_POST['p_price'];
        $p_id=$_POST['p_id'];
        $model=new Price_class();
        $str=$model->updates($p_id,$p_price);
        if($str!=0){
            echo '1';
        }else{
            echo '0';
        }
    }

}