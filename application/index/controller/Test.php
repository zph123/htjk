<?php
/**
 * Created by PhpStorm.
 * User: SVector
 * Date: 2016.11.1
 * Time: 20:10
 */

namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use app\index\model\Gl_test;
use app\index\model\onlinetest as onlinetestModel;

class Test extends Controller
{
    /**作者：李斌
     * 展示页面
     */
    public function onlinetest()
    {

        //从数据库获取 测试费用 、预测身高的费用

        return view('onlineTest');
    }

    /**ajax判断用户是否已经登录
     *
     */
    public function ajax_login_status(){
        if (session('?uid'))
            echo 1;
        else echo 0;
    }
    /**作者：李斌
     *
     */
    public function add_onlinetest()
    {
        /**添加一个测试人的信息入库
         * 方案：
         * 0.先判断用户是否已经提交过未处理的数据
         * 1.先将数据集存入session（设置超时时间）或MySQL
         * 2.首先判断用户是否登录
         * 3.1如果已经登录就调用支付，支付成功就将session中的数据入MySQL，并销毁该session，否则超时后提示操作超时以及某些页面询问用户是否取消操作
         * 3.2MySQL方案：类似京东商城购物车，......
         *
         * 如果没登录就调用登录和注册，登录或注册完毕后
         */
        $infos=input();

//        if(isset()){
//
//        }else{
//
//        }
//        var_dump($infos);die;
        if ($result = onlinetestModel::create($infos)) {
            return '提交成功';
        } else {
            return $result->getError();
        }
    }
    /*作者：刘志祥
     * 2016-11-2 09:34:16
     * 活动列表
     */
    public function nowList()
    {
        header('content-type:text/html;charset=utf-8');
        $below_list = Db::table('below_list')->where('l_stime','>',date("Y-m-d H:i:s"))->order('l_stime','asc')->select();
        foreach($below_list as $key=>$val){
            $below_list[$key]['price'] = Db::table('price_class')->where('p_id','in','2,3')->select();
        }
        return view('nowList',['below_list'=>$below_list]);
    }
    /*作者：刘志祥
     * 2016-11-1 10:50:12
     * 现场测试
     */
    public function nowTest()
    {
        //获取登录人id
        $id = Session::get('uid');
        header('content-type:text/html;charset=utf-8');
        $below_list = Db::table('below_list')->where('l_etime','>',date("Y-m-d H:i:s"))->order('l_stime','asc')->find();
        $below_list['price'] = Db::table('price_class')->where('p_id','in','2,3')->select();

        return view('nowTest',['below_list'=>$below_list,'id'=>$id]);
    }
    /*
     * @作者：刘志祥
     */
    public function nowTest_pro(){
        header('content-type:text/html;charset=utf-8');
        //获取登录人id

        $data = $_POST;
        //后台验证  验证是否在当前时间
        $l_time = Db::table('below_list')->where('l_id','=',$data['l_id'])->find();
        $time1=date("Y-m-d H:i:s");
        if($time1<$l_time['l_stime'] || $time1>$l_time['l_etime']){
            $this->error('还未到活动时间，请您耐心等待^_^');die;
        }
        //后台验证  先验证唯一
        $time=date("Y-m-d");
        $data['n_time']=$time;
        $id_number = Db::table('nowtest')->where('n_idd','=',$data['id_number'])->where('n_time','=',$time)->find();
        if($id_number){
            $this->error('新增失败,请确认您是否已经提交过了');die;
        }
        // 需要 根据$data['l_id'] 联查 活动表 联查价格表   便于后期查询总价
        $prices = Db::table('price_class')->where('p_id','in','2,3')->select();
        foreach($prices as $val){
            if($val['p_id']==2){
                $data['l_price'] = $val['p_price'];
            }elseif($val['p_id']==3){
                $data['l_height'] = $val['p_price'];
            }
        };
        //统计 总价
        if($data['predict_height']==1){
            $data['l_price'] = $data['l_price']+$data['l_height'];
        };
        //判断男女
        if($data['gender']==1){
            $data['spermatorrhea'] = $data['menarche'];
        }
        $id = Session::get('uid');
        $data['uid'] = $id;
        $test = new Gl_test();
        $res = $test -> add_one($data);
        if($res){
            //添加成功跳转到支付页面
            $this->success('提交成功，正在跳转支付页面', 'index/test/nowTest');
        }else{
            echo "This is error.";
        }
    }
}