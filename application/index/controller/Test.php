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
use think\Config;
use think\Request;
use think\Session;
use app\index\model\Gl_test;
use app\index\model\onlinetest as onlinetestModel;
use app\index\model\Order as orderModel;
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

        return view('onlineTest',
            [
                'price'=>$price,
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
    public function ajax_price(){
        $status=input('predict_height');
        if($status==1)
            $price_id=6;
        elseif($status==0)
            $price_id=1;
        $price=price_classModel::get($price_id);
        echo json_encode($price);
    }
    /**作者：李斌
     * 数据入库，调用支付
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
        //获取当前付费项 总价
        if($infos['predict_height']==1)
            $price_id=6;
        elseif($infos['predict_height']==0)
            $price_id=1;
        $price=price_classModel::get($price_id);
        //付费项总价
        $infos['price']=$price['p_price'];
        //付费项名称
        $infos['items']=$price['p_name'];

        /**保存上传来的图片
         */
        //获取引子
        $file = $request->file('hands_photo');
        //读取路径配置
        $file_path=Config::get('uploads.customer_uploads');
        //如果路径不存在，则生成
        if(!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        // 移动到框架应用根目录/public/customer_uploads/ 目录下
        $file_info = $file->move($file_path);
        if ($file_info) {
            $infos['hands_photo_path']=$file_info->getSaveName();
        } else {
            // 上传失败获取错误信息，并显示
            $this->error($file->getError());
        }

        //预留：可以考虑为用户做一个重复提交判断
//        if(isset()){
//        }else{
//        }
        //先在总订单表进行注册，并获取注册ID
        if($res=orderModel::create_o_id(3)){
            $infos['o_id']=$res['o_id'];
        } else {
            return $res->getError();
        }

        if ($result = onlinetestModel::create($infos)) {

            /**生成订单信息 向 支付接口发送订单信息 并附送订单信息
             * body(商品名或订单描述),
             * out_trade_no（一般为订单号）
             * total_fee（订单金额，单位“分”，要注意单位问题）
             */
            $pay_data=array([
                'body'=>$infos['items'],
                'out_trade_no'=>$res['out_trade_no'],
                'total_fee'=>$infos['price']
            ]);
            return '数据已准备，等待支付接口！';
        } else {
            return $result->getError();
        }
    }

    /**作者：李斌
     * 为用户展示消费项（包括免费项）
     */
    public function spent_list(){
        /**主体信息包括
         * 测试人姓名、消费项名称、消费项金额、消费项生成时间、付费方式、
         * 要求在点击简略信息后，展示详细信息
         */
        $uid=session::get('uid');
        //查询 现场测试 的消费记录
        //查询 在线测试 的消费记录
        //查询 预测身高 的消费记录
        //查询 运动处方 的消费记录
        //查询 营养处方 的消费记录
        $price=onlinetestModel::get($uid);
    }

    /**作者：李斌
     * 为用户的消费项，展示单项内的详细信息
     */
    public function details_spent(){
        /**
         * 测试人姓名、测试项名称、图片、价格、消费时间
         */

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