<?php
namespace app\index\controller;
use think\Cookie;
use think\Db;
use think\Request;
use app\index\model\user_model;

class User extends Common
{
    public function index()
    {
        $id=Cookie::get('uid');
        $user=Db::table('gl_users')->where('id',$id)->find();
        Cookie::set('username', $user['name']);
        $this->assign('name',$user['name']);
        $this->assign('out','true');
        return view('index/userCenter');
    }
    /**
     * 加载营养均衡首页
     */
    function nutrition(){
        $id=Cookie::get('uid');
        $type='1';
        $field='o_id,out_trade_no,addtime';
        $model=new user_model();
        $data=$model->user_order($id,$type,$field);
        if(empty($data)){
            $data='0';
        }
        $this->assign('list', $data);
        return $this->fetch();
    }
    /**
     * 测试报告
     */
    function report(){
        $id=Cookie::get('uid');
        $field='o_id,out_trade_no,addtime';
        $data=Db::table('order')
        ->where('u_id',$id)
        ->where('type','in','3,4')
        ->order('addtime DESC')
        ->column($field);
        if(empty($data)){
            $data='0';
        }
        $this->assign('list', $data);
        return $this->fetch('nutrition');
    }
    /**
     *运动处方
     */
    function motion(){
        $id=Cookie::get('uid');
        $type='2';
        $field='o_id,out_trade_no,addtime';
        $model=new user_model();
        $data=$model->user_order($id,$type,$field);
        if(empty($data)){
            $data='0';
        }
        $this->assign('list', $data);
        return $this->fetch('nutrition');
    }
    /**
     * 查看详情
     */
    function see(Request $request){
        $uid=Cookie::get('uid');
        if(isset($uid)){
            $arr=$request->param();
            $id=empty($arr['r'])?"":$arr['r'];
            $data=Db::table('order')
            ->where('u_id',$uid)
            ->where('o_id',$id)
            ->find();
            $is_pay=$data['is_pay'];
            $way_tr='';
            if($data){
                if($data['is_pay']==0){
                    $data['is_pay']='未支付';
                }else{
                    $data['is_pay']='已支付';
                }
                if($data['status']==0){
                    $data['status']='未生成';
                }else{
                    if($data['type']==1){
                        $way=Db::table('nutrition_way')->where('o_id',$id)->find();
                        $way_tr['每天摄入的主食量（指加工后的食物重量）']='';
                        $way_tr['米饭']=$way['mifan_mrpj'];
                        $way_tr['馒头']=$way['mantou_mrpj'];
                        $way_tr['烙饼']=$way['laobing_mrpj'];
                        $way_tr['面条']=$way['miantiao_mrpj'];
                        $way_tr['杂粮（土豆、玉米、红薯等）']=$way['zaliang_mrpj'];
                        $way_tr['每天摄入蛋类(个) ']=$way['nai_mrpj'];
                        $way_tr['每天摄入奶制品数量（袋/200或250ml）']=$way['danlei_mrpj'];
                        $way_tr['每天饮水量']=$way['yinshui_mrpj'];
                        $way_tr['每天摄入的蔬菜数量']=$way['shucai_mrpj'];
                        $way_tr['每天摄入的水果数量/min)']=$way['shuiguo_mrpj'];
                        $way_tr['每天摄入的禽畜肉量']=$way['rouliang_mrpj'];
                        $way_tr['每天摄入的水产品量(鱼、虾类)']=$way['shuichan_mrpj'];
                        $way_tr['每天摄入的豆制品量']=$way['douzhi_mrpj'];
                        $way_tr['每天摄入的粗粮食品量']=$way['culiang_mrpj'];
                        $way_tr['每天摄入的肥肉或油脂品量']=$way['feirou_mrpj'];
                        $way_tr['每天摄入的零食量']=$way['lingshi_mrpj'];
                        $way_tr['每天摄入的油炸、烧烤、动物内脏类食物']=$way['youzha_mrpj'];
                        $way_tr['每天摄入的加工食品(腌酱腊制品、罐头、方便食品）']=$way['jiagong_mrpj'];
                        $way_tr['主食类']=$way['zhushi_mzpj'];
                        $way_tr['蔬菜类']=$way['shucai_mzpj'];
                        $way_tr['禽畜肉类']=$way['rouliang_mzpj'];
                        $way_tr['水产类']=$way['shuichan_mzpj'];
                        $way_tr['豆品类、奶类']=$way['douzhi_mzpj'];
                        $way_tr['果品类']=$way['guoping_mzpj'];
                        $way_tr['营养补充品类(如蛋白粉、维生素等)']=$way['yingyang_mzpj'];
                        $way_tr['其他类(烧烤、腌腊卤制品、罐头、方便食品)']=$way['qita_mzpj'];
                        $way_tr['在过去的 7 天，平均每天喝几瓶碳酸饮料']=$way['tansuan_xgpj'];
                        $way_tr['在过去的 7 天，吃过几次甜点']=$way['tiandian_xgpj'];
                        $way_tr['在过去的 7 天，去过几次西式快餐店']=$way['xican_xgpj'];
                        $way_tr['在过去的 7 天，有几天喝过牛奶或豆浆或酸奶']=$way['nai_xgpj'];
                        $way_tr['在过去的 7 天，有几天没吃早餐']=$way['zaocan_xgpj'];
                        $way_tr['是否会为了减肥而减少摄入的食物量']=$way['jianfei_xgpj'];
                        $way_tr['是否有吃夜宵的习惯']=$way['yexiao_xgpj'];
                        $way_tr['食欲如何']=$way['shiyu_xgpj'];
                        $way_tr['家里做菜以什么方法为主']=$way['zuocai_xgpj'];
                        $way_tr['平时吃饭是什么情况']=$way['chifan_xgpj'];
                        $way_tr['饮食口味属于哪种']=$way['kouwei_xgpj'];
                        $way_tr['营养均衡状况评价']=$way['yingyang_pj'];
                        $way_tr['主食类增减建议']=$way['zhushi_jy'];
                        $way_tr['优质蛋白质摄入建议']=$way['danbaizhi_jy'];
                        $way_tr['脂类摄入建议']=$way['zhilei_jy'];
                        $way_tr['蔬果摄入建议']=$way['shuguo_jy'];
                        $way_tr['其他食物摄入建议']=$way['qita_jy'];
                        $way_tr['饮食习惯改进建议']=$way['yinshi_jy'];
                        $way_tr['特殊建议']=$way['teshu_jy'];
                    }else if($data['type']==2){
                        $way=Db::table('motion_way')->where('u_id',$uid)->where('o_id',$id)->find();
                        $way_tr['姓名']=Cookie::get('username');
                        $way_tr['性别']=$way['sex']==1?'男':'女';
                        $way_tr['编号']=$way['number'];
                        $way_tr['周数']=$way['week'];
                        $way_tr['出生年月日']=$way['birth'];
                        $way_tr['日历年龄']=$way['age'];
                        $way_tr['骨龄']=$way['boneage'];
                        $way_tr['生长发育类型']=$way['grow'];
                        $way_tr['身高']=$way['height'];
                        $way_tr['体重']=$way['weight'];
                        $way_tr['试验中达到的最高心率（次/min)']=$way['heartrate'];
                        $way_tr['靶心率(次/min)']=$way['blank'];
                        $way_tr['血压']=$way['pressure'];
                        $way_tr['心率监护低限（次/10s）']=$way['lowlimit'];
                        $way_tr['心率监护高限（次/10s）']=$way['highlimit'];
                        $way_tr['运动目的']=$way['objective'];
                        $way_tr['运动项目']=$way['project'];
                        $way_tr['运动强度']=$way['exerciseintensity'];
                        $way_tr['准备运动']=$way['readytoexercise'];
                        $way_tr['基本运动']=$way['basicmovement'];
                        $way_tr['整理运动']=$way['finishingmovement'];
                        $way_tr['每次运动时间']=$way['timeofmovement'];
                        $way_tr['每周运动频率']=$way['weeklyexercisetime'];
                    }else if($data['type']==3){
                        $way=Db::table('online_report')
                        ->alias('a')
                        ->join('onlinetest w','a.or_id = w.o_id')
                        ->where('or_id',$id)
                        ->find();
                        $way_tr['单位']=$way['school'];
                        $way_tr['年龄']=$this->birthday($way['appDate']);
                        $way_tr['姓名']=$way['customer'];
                        $way_tr['性别']=$way['gender']==1?'男':'女';
                        $way_tr['出生日期']=$way['appDate'];
                        $way_tr['测试时间']=$way['addtime'];
                        $way_tr['测试序号']=$way['tdnumber'];
                        $way_tr['骨龄片号']=$way['glnumber'];
                        $height=json_decode($way['height'],true);
                        $way_tr['身高']=$height[0]['height'].'cm';
                        $way_tr['评价']=$height[0]['appraise'];
                        $way_tr['发育类型']=$height[0]['development'];
                        $weight=json_decode($way['weight'],true);
                        $way_tr['体重']=$weight[0]['weight'].'kg';
                        $way_tr['评价']=$weight[0]['appraise'];
                        $way_tr['发育类型']=$weight[0]['development'];
                        $chest=json_decode($way['chest'],true);
                        $way_tr['胸围']=$chest[0]['chest'].'cm';
                        $way_tr['评价']=$chest[0]['appraise'];
                        $way_tr['发育类型']=$chest[0]['development'];
                        $gpbone=json_decode($way['gpbone'],true);
                        $way_tr['GP骨龄']=$gpbone[0]['gpbone'].'岁-月';
                        $way_tr['评价']=$gpbone[0]['appraise'];
                        $way_tr['发育类型']=$gpbone[0]['development'];
                        $chnbone=json_decode($way['chnbone'],true);
                        $way_tr['CHN骨龄']=$chnbone[0]['chnbone'].'岁-月';
                        $way_tr['评价']=$chnbone[0]['appraise'];
                        $way_tr['发育类型']=$chnbone[0]['development'];
                        $ch05bone=json_decode($way['ch05bone'],true);
                        $way_tr['CH05骨龄']=$ch05bone[0]['ch05bone'];
                        $way_tr['评价']=$ch05bone[0]['appraise'];
                        $way_tr['发育类型']=$ch05bone[0]['development'];
                        $tw3c=json_decode($way['tw3c'],true);
                        $way_tr['TW3C']=$tw3c[0]['tw3c'];
                        $way_tr['评价']=$tw3c[0]['appraise'];
                        $way_tr['发育类型']=$tw3c[0]['development'];
                        $tw3r=json_decode($way['tw3r'],true);
                        $way_tr['TW3R']=$tw3r[0]['tw3r'];
                        $way_tr['评价']=$tw3r[0]['appraise'];
                        $way_tr['发育类型']=$tw3r[0]['development'];
                        $way_tr['遗传身高']=$way['h_height'].'CM';
                        $way_tr['BSU-CHN预测身高']=$way['chn_height'].'CM';
                        $way_tr['BSU-GP预测身高']=$way['gp_height'].'CM';
                        $way_tr['BP预测身高']=$way['bp_height'].'CM';
                        $way_tr['RWT预测身高']=$way['rwt_height'].'CM';
                        if($way['predict_height']==1){
                            $way_tr['专家综合评测身高']=$way['evaluating'].'±2CM';
                        }
                    }else if($data['type']==4){
                        $way=Db::table('online_report')
                        ->alias('a')
                        ->join('nowtest w','a.or_id = w.o_id')
                        ->where('or_id',$id)
                        ->find();
                        $way_tr['单位']=$way['n_school'];
                        $way_tr['年龄']=$this->birthday($way['n_date']);
                        $way_tr['姓名']=$way['n_name'];
                        $way_tr['性别']=$way['n_sex']==1?'男':'女';
                        $way_tr['出生日期']=$way['n_date'];
                        $way_tr['测试时间']=$way['n_time'];
                        $way_tr['测试序号']=$way['tdnumber'];
                        $way_tr['骨龄片号']=$way['glnumber'];
                        $height=json_decode($way['height'],true);
                        $way_tr['身高']=$height[0]['height'].'cm';
                        $way_tr['评价']=$height[0]['appraise'];
                        $way_tr['发育类型']=$height[0]['development'];
                        $weight=json_decode($way['weight'],true);
                        $way_tr['体重']=$weight[0]['weight'].'kg';
                        $way_tr['评价']=$weight[0]['appraise'];
                        $way_tr['发育类型']=$weight[0]['development'];
                        $chest=json_decode($way['chest'],true);
                        $way_tr['胸围']=$chest[0]['chest'].'cm';
                        $way_tr['评价']=$chest[0]['appraise'];
                        $way_tr['发育类型']=$chest[0]['development'];
                        $gpbone=json_decode($way['gpbone'],true);
                        $way_tr['GP骨龄']=$gpbone[0]['gpbone'].'岁-月';
                        $way_tr['评价']=$gpbone[0]['appraise'];
                        $way_tr['发育类型']=$gpbone[0]['development'];
                        $chnbone=json_decode($way['chnbone'],true);
                        $way_tr['CHN骨龄']=$chnbone[0]['chnbone'].'岁-月';
                        $way_tr['评价']=$chnbone[0]['appraise'];
                        $way_tr['发育类型']=$chnbone[0]['development'];
                        $ch05bone=json_decode($way['ch05bone'],true);
                        $way_tr['CH05骨龄']=$ch05bone[0]['ch05bone'];
                        $way_tr['评价']=$ch05bone[0]['appraise'];
                        $way_tr['发育类型']=$ch05bone[0]['development'];
                        $tw3c=json_decode($way['tw3c'],true);
                        $way_tr['TW3C']=$tw3c[0]['tw3c'];
                        $way_tr['评价']=$tw3c[0]['appraise'];
                        $way_tr['发育类型']=$tw3c[0]['development'];
                        $tw3r=json_decode($way['tw3r'],true);
                        $way_tr['TW3R']=$tw3r[0]['tw3r'];
                        $way_tr['评价']=$tw3r[0]['appraise'];
                        $way_tr['发育类型']=$tw3r[0]['development'];
                        $way_tr['遗传身高']=$way['h_height'].'CM';
                        $way_tr['BSU-CHN预测身高']=$way['chn_height'].'CM';
                        $way_tr['BSU-GP预测身高']=$way['gp_height'].'CM';
                        $way_tr['BP预测身高']=$way['bp_height'].'CM';
                        $way_tr['RWT预测身高']=$way['rwt_height'].'CM';
                        if($way['n_height']==1){
                            $way_tr['专家综合评测身高']=$way['evaluating'].'±2CM';
                        }
                    }
                    $data['status']='已生成';
                }
            }
            $this->assign('way',$way_tr);
            $this->assign('is_pay',$is_pay);
            $this->assign('list', $data);
            return $this->fetch('content');
        }        
    }
    //根据出生日期计算年龄
    function birthday($birthday){ 
     $age = strtotime($birthday); 
     if($age === false){ 
      return false; 
     } 
     list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age)); 
     $now = strtotime("now"); 
     list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now)); 
     $age = $y2 - $y1; 
     if((int)($m2.$d2) < (int)($m1.$d1)) 
      $age -= 1; 
     return $age; 
    } 
    /**
     * 去支付
     */
    function pay(Request $request){
        $uid=Cookie::get('uid');
        $arr=$request->param();
        $id=empty($arr['r'])?"":$arr['r'];
        $data=Db::table('order')
        ->where('u_id',$uid)
        ->where('o_id',$id)
        ->find();
        if($data){
            if($data['is_pay']==0){
                Cookie::set('out_trade_no', $data['out_trade_no']);
                Cookie::set('price', $data['amount']);
                $this->redirect("http://htjk.zphteach.com/html/WxpayAPI_php_v3/example/jsapi.php");
                //$this->redirect("http://www.zphteach.com/htjk/WxpayAPI_php_v3/example/jsapi.php?trade=$data[out_trade_no]");
                // var_dump($data);
            }else{
                  echo 'is_pay  1'; 
            }            
        }else{
            echo 'error';
        }
        
    }
    /**
     *删除订单
     */
    function user_delete(Request $request){
        $uid=Cookie::get('uid');
        $arr=$request->param();
        $id=empty($arr['r'])?"":$arr['r'];
        $data=Db::table('order')
        ->where('u_id',$uid)
        ->where('o_id',$id)
        ->find();
        if($data){
            $re=Db::table('order')->where('u_id',$uid)->where('o_id',$id)->delete();
            if($re){
                if($data['type']==1){
                    $action='nutrition';
                    $type=Db::table('nutrition_order')->where('o_id',$id)->delete();
                }else if($data['type']==2){
                    $action='motion';
                    $type=Db::table('motion_order')->where('order_id',$id)->delete();
                }else if($data['type']==3){
                    $action='report';
                    $type=Db::table('onlinetest')->where('o_id',$id)->delete();
                }else if($data['type']==4){
                    $action='report';
                    $type=Db::table('nowtest')->where('o_id',$id)->delete();
                }
                $this->redirect("$action");
            }else{
                $this->error('订单删除失败');
            }
        }else{
            $this->error('该订单不存在');
        }
    }
    /**
     * 退出登录
     */
    function quit(){
        Cookie::set('uid',null);
        $this->redirect('index/index');
    }
}
