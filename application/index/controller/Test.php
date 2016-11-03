<?php
/**
 * Created by PhpStorm.
 * User: SVector
 * Date: 2016.11.1
 * Time: 20:10
 */

namespace app\index\controller;

use app\index\model\onlinetest as onlinetestModel;

class Test
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
        if (session('?name'))
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
    public function nowList(){
        header('content-type:text/html;charset=utf-8');
        $below_list = Db::table('below_list')
            ->where('l_stime','>',date("Y-m-d H:i:s"))
            ->order('l_stime','asc')
            ->select();
        return view('nowList',['below_list'=>$below_list]);
    }
    /*作者：刘志祥
     * 2016-11-1 10:50:12
     * 现场测试
     */
    public function nowTest()
    {
        header('content-type:text/html;charset=utf-8');
        $below_list = Db::table('below_list')
            ->where('l_stime','>',date("Y-m-d H:i:s"))
            ->order('l_stime','asc')
            ->find();
        return view('nowTest',['below_list'=>$below_list]);
    }
}