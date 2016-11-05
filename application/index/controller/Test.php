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
use think\Request;
use think\Session;
use app\index\model\Gl_test;
use app\index\model\onlinetest as onlinetestModel;
use app\index\model\price_class as price_classModel;

class Test extends Controller
{
    /**作者：李斌
     * 展示页面
     */
    public function onlinetest()
    {

        //从数据库获取 测试费用
        $price_id=1;
        $price=price_classModel::get($price_id);
        $original_price=$price['p_price'];

        //模拟暂定 折扣 及 折扣价
        $discount=5;
        $discount_price=0.1*$discount*$original_price;

        return view('onlineTest',
            [
                'original_price'=>$original_price,
                'discount'=>$discount,
                'discount_price'=>$discount_price
            ]
        );
    }

    /**作者：李斌
     * ajax判断用户是否已经登录
     */
    public function ajax_login_status(){
        if (session('?uid'))
            echo 1;
        else echo 0;
    }

    /**作者：李斌
     * ajax获取 “预测身高” 的价格
     */
    public function ajax_add_price(){
        $price_id=3;
        $price=price_classModel::get($price_id);
        echo $price['p_price'];
    }
    /**作者：李斌
     * 数据入库
     */
    public function add_onlinetest(Request $request)
    {
        /**添加一个测试人的信息入库
         * 方案A：
         * 0.直接入库
         * 方案B：
         * 0.先判断用户是否已经提交过未处理的数据
         * 1.先将数据集存入session（设置超时时间）或MySQL
         * 2.首先判断用户是否登录
         * 3.1如果已经登录就调用支付，支付成功就将session中的数据入MySQL，并销毁该session，否则超时后提示操作超时以及某些页面询问用户是否取消操作
         * 3.2MySQL方案：类似京东商城购物车，......
         *
         * 如果没登录就调用登录和注册，登录或注册完毕后
         */

    /**
     * 方案 A
     */
        $infos=input();
        //状态：已提交，未付费
        $infos['status']=0;
        //APP用户ID
        $infos['uid']=session('uid');

        $file = $request->file('hands_photo');
        $file_path=ROOT_PATH . 'public' . DS . 'customer_uploads';
        if(!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        $file_info = $file->move($file_path);
        if ($file_info) {
            $infos['hands_photo_path']=$file_info->getSaveName();
        } else {
            // 上传失败获取错误信息
            $this->error($file->getError());
        }

        //预留：可以考虑为用户做一个重复提交判断
//        if(isset()){
//
//        }else{
//
//        }

        if ($result = onlinetestModel::create($infos)) {

            /**生成订单信息 向 支付接口发送订单信息 并附送订单信息
             * body(商品名或订单描述),
             * out_trade_no（一般为订单号）
             * total_fee（订单金额，单位“分”，要注意单位问题）
             */
            $body='3';
            $out_trade_no='1';
            $total_fee='2';
            $pay_array=array(
                'body'=>$body,
                'out_trade_no'=>$out_trade_no,
                'total_fee'=>$total_fee,
            );
            return "数据已准备，等待连接支付接口！";
//            return redirect("index/test/ajax_login_status",$pay_array);
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
        $price = Db::table('price_class')->where('p_id','in','2,3')->select();
        return view('nowList',['below_list'=>$below_list,'price'=>$price]);
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
        $price = Db::table('price_class')->where('p_id','in','2,3')->select();
        return view('nowTest',['below_list'=>$below_list,'id'=>$id,'price'=>$price]);
    }
    /*
     * @作者：刘志祥
     */
    public function nowTest_pro(){
        header('content-type:text/html;charset=utf-8');
        $data = $_POST;
        //后台验证  如果没有活动 而点击报名 。。则会提示“没有活动，暂时不能报名”
        if(empty($data['l_id'])){
            $this->redirect('index/test/nowTest');die;
        }
        //后台验证  验证是否在当前时间
        $l_time = Db::table('below_list')->where('l_id','=',$data['l_id'])->find();
        $time1=date("Y-m-d H:i:s");
        if($time1<$l_time['l_stime'] || $time1>$l_time['l_etime']){
            $this->redirect('index/test/nowTest');die;
        }
        //后台验证  先验证唯一
        $time=date("Y-m-d");
        $data['n_time']=$time;
        $id_number = Db::table('nowtest')->where('n_idd','=',$data['id_number'])->where('n_time','=',$time)->find();
        if($id_number){
            $this->redirect('index/test/nowTest');die;
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
            $this->redirect('index/test/nowTest');
        }else{
            echo "This is error.";
        }
    }
}