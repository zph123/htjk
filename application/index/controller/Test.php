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
use think\Cookie;
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
        $price1 = DB::table('price_class')->where(['p_id'=>1])->find();
        $price2 = DB::table('price_class')->where(['p_id'=>2])->find();
        $controller = "Test";
        $action = "onlinetest";
        Cookie::set('controller', $controller);
        Cookie::set('action', $action);
        $id = Cookie::get('uid');
        if($id){
            $log = 1;
            $info = Db::table('user_infos')->where('u_id',$id)->find();
            $user = Db::table('gl_users')->where('id',$id)->find();
        }else{
            $log = 0;
            $info = null;
            $user = null;
        }
        return view('online',
            [
                'price1'=>$price1,
                'price2'=>$price2,
                'user'=>$user,
                'log'  =>$log,
                'info' =>$info
            ]
        );
    }
    /**作者：李斌
     * ajax判断用户是否已经登录
     */
    public function ajax_login_status(){
        if (Cookie('?uid'))
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
         * 1.先将数据集存入Cookie（设置超时时间）或MySQL
         * 2.首先判断用户是否登录
         * 3.1如果已经登录就调用支付，支付成功就将Cookie中的数据入MySQL，并销毁该Cookie，否则超时后提示操作超时以及某些页面询问用户是否取消操作
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
        else return "非法数据提交！";
        $price=price_classModel::get($price_id);
        //付费项总价
        $infos['price']=$price['p_price'];
        //付费项名称
        $infos['items']=$price['p_name'];

        /**保存上传来的图片
         */
        //获取引子
        $file = $request->file('hands_photo');
        //检测文件大小，检定是否为非法数据提交
        if(!$file->checkSize(5242880))
            return "非法数据提交！";
        //读取路径配置
        $file_path=Config::get('uploads.customer_uploads');
        //如果路径不存在，则生成
        if(!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        //将图片转移到 框架应用根目录/public/customer_uploads/ 目录下
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
        $id = Cookie::get('uid');
        $info = Db::table("user_infos")->where('u_id',$id)->find();
        $phone = Db::table('gl_users')->field('phone,name')->where('id',$id)->find();
        $info['appDate'] = $info['birthday'];
        unset($info['birthday']);
        list($year,$month,$day) = explode("-",$info['appDate']);
        $year_diff = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff  = date("d") - $day;
        if ($day_diff < 0 || $month_diff < 0)
            $year_diff--;
        $info['age'] = $year_diff;
        unset($info['u_id']);
        $data = array_merge($info,$infos);
        $data['contact_phone'] = $phone['phone'];
        $data['customer'] = $phone['name'];
        $data['addtime'] = date('Y-m-d H:i:s',time());
        //先在总订单表进行注册，并获取注册ID
        if($res=orderModel::create_o_id(3,$infos['price'])){
            $data['o_id']=$res['o_id'];
        } else {
            return $res->getError();
        }

        if ($result = onlinetestModel::create($data)) {

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
            return redirect('index/user/see',['r'=>$data['o_id']]);
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
        $time=date("Y-m-d H:i:s");
        $below_list = Db::table('below_list')->where('l_stime','>',$time)->order('l_stime','asc')->select();
        $price = Db::table('price_class')->where('p_id','in','2')->select();
        return view('nowList',['below_list'=>$below_list,'price'=>$price]);
    }

    /*作者：刘志祥
     * 2016-11-1 10:50:12
     * 现场测试
     */
    public function nowTest()
    {
        $controller = "Test";
        $action = "nowTest";
        Cookie::set('controller', $controller);
        Cookie::set('action', $action);
        //获取登录人id
        $id = Cookie::get('uid');
        if($id){
            $log = 1;
            $info = Db::table('user_infos')->where('u_id',$id)->find();
        }else{
            $log = 0;
            $info = null;
        }
        $below_list = Db::table('below_list')->where('l_etime','>',date("Y-m-d H:i:s"))->where('l_status','neq','0')->order('l_stime','asc')->find();
        $price = Db::table('price_class')->where('p_id',2)->find();
        return view('nowTest',['log'=>$log,'below_list'=>$below_list,'id'=>$id,'price'=>$price,'info'=>$info]);
    }
    /*
     * @作者：刘志祥
     */
    public function nowTest_pro(Request $request){
        $data = $request->post();
        //后台验证  如果没有活动 而点击报名 。。则会提示“没有活动，暂时不能报名”
        if(empty($data['l_id'])){
            $this->redirect('index/test/nowTest');die;
        }
        $id = Cookie::get('uid');
        $info = Db::table('user_infos')->where('u_id',$id)->find();

        //后台验证  先验证唯一
        $time=date("Y-m-d");
        $data['n_time']=$time;
        $id_number = Db::table('nowtest')->where('n_idd','=',$info['id_number'])->where('l_id',$data['l_id'])->find();
        if($id_number){
           echo 0;die;
        }
        // 需要 根据$data['l_id'] 联查 活动表 联查价格表   便于后期查询总价
        $prices = Db::table('price_class')->where('p_id','in',[2,7])->select();
        if($data['predict_height']==1){
            $data['l_price'] = $prices[1]['p_price'];
        }else{
            $data['l_price'] = $prices[0]['p_price'];
        }
        $usermsg = Db::table('gl_users')->field('name,phone')->where("id",$id)->find();
        $data['uid'] = $id;
        //实例化Model层——》Order
        $test = new orderModel();
        $o_id = $test -> nowTest($id,$data['l_price']);
        $data['o_id'] = $o_id;


        list($year,$month,$day) = explode("-",$info['birthday']);
        $year_diff = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff  = date("d") - $day;
        if ($day_diff < 0 || $month_diff < 0)
            $year_diff--;



        $dat['l_id'] = $data['l_id'];//关联活动列表id
        $dat['uid'] = $data['uid'];//登录人id
        $dat['n_price'] = $data['l_price'];//总价钱（初始价钱+预测身高的价钱）
        $dat['n_sex'] = $info['gender'];//测试人性别
        $dat['n_name'] = $usermsg['name'];//测试人名字
        $dat['n_date'] = $info['birthday'];//测试人出生日期
        $dat['n_idd'] = $info['id_number'];//测试人身份证号
        $dat['n_age'] = $year_diff;//测试人年龄
        $dat['n_stature'] = $info['birth_height'];//测试人身高
        $dat['n_weight'] = $info['birth_weight'];//体重
        $dat['n_eutocia'] = $info['birth_smoothly'];//是否顺产出生
        $dat['n_gonacratia'] = $data['menarche'];//是否已遗精 or 初潮
        $dat['n_phone'] = $usermsg['phone'];//手机号
        $dat['n_email'] = $info['email'];//邮箱
        $dat['n_address'] = $info['contact_address'];//联系地址
        $dat['n_fstature'] = $info['father_height'];//父亲身高
        $dat['n_mstature'] = $info['mother_height'];//母亲身高
        $dat['n_paper'] = $data['need_report'];//纸质报告
        $dat['n_school'] = $info['school'];//测试人学校
        $dat['n_time'] = $data['n_time'];//测试时间
        $dat['n_height'] = $data['predict_height'];//预测身高状态
        $dat['o_id'] = $data['o_id'];//


        $res =  Db::table('nowtest')->insert($dat);
        if($res){
            echo $dat['o_id'];
        }else{
            echo -1;
        }
    }

    /**
     * @author lzy
     */
    public function ajaxcheck($l_id)
    {
        $l_info =  Db::table('below_list')->where('l_id',$l_id)->find();
        if(!$l_info){
            echo 1;die;
        }elseif ($l_info['l_status']==2){
            echo 2;die;
        }elseif(time()>strtotime($l_info['l_stime'])){
            echo 3;die;
        }else{
            echo 5;die;
        }
    }

    public function nolog(Request $request)
    {
        $tell = $request->post('tell');
        $l_id = $request->post('l_id');
        $re = Db::table("activity")->where(['tell'=>$tell,'l_id'=>$l_id])->find();
        if($re){
            echo 0;
        }else{
            $data = $request->post();
            $res = Db::table('activity')->insert($data);
            if($res){
                Db::table('below_list')
                    ->where('l_id', $l_id)
                    ->setInc('l_apply');
                echo 1;
            }else{
                echo 2;
            }
        }
    }

    public function ajax_prices(){
        $status=input('predict_height');
        if($status==1)
            $price_id=7;
        elseif($status==0)
            $price_id=2;
        $price=price_classModel::get($price_id);
        echo json_encode($price);
    }
}