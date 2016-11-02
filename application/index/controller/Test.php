<?php
/**
 * Created by PhpStorm.
 * User: SVector
 * Date: 2016.11.1
 * Time: 20:10
 */

namespace app\index\controller;
use think\Db;

class Test extends Common
{
    /**作者：李斌
     * 展示页面
     */
    public function onlinetest()
    {
        return view('onlineTest');
    }

    /**作者：李斌
     *
     */
    public function add_onlinetest()
    {

    }

    /*作者：刘志祥
     * 2016-11-2 09:34:16
     * 活动列表
     */
    public function nowList(){
        header('content-type:text/html;charset=utf-8');
        $below_list = Db::table('below_list')->where('l_stime','>',date("Y-m-d H:i:s"))->order('l_stime','asc')->select();
        return view('nowList',['below_list'=>$below_list]);
    }
    /*作者：刘志祥
     * 2016-11-1 10:50:12
     * 现场测试
     */
    public function nowTest()
    {
        header('content-type:text/html;charset=utf-8');
        $below_list = Db::table('below_list')->where('l_stime','>',date("Y-m-d H:i:s"))->order('l_stime','asc')->find();
        return view('nowTest',['below_list'=>$below_list]);
    }
}