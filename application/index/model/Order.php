<?php
namespace app\index\model;


use think\Model;
use think\Db;
use think\Cookie;

class Order extends Model
{
    //防碰撞验证
    public static function createuniquenumber() {
        //商户id
        $num = "1280101701";
        $str = $num.date("YmdHis",time());
        $re = Db::table("order")
            ->where("out_trade_no", $str)
            ->find();
        if($re){
            return self::createuniquenumber();
        }else{
            return $str;
        }
    }

    //生成订单，并返回自增ID
    public static function create_o_id($type,$amount){
        $out_trade_no=self::createuniquenumber();
        $addtime=date("YmdHis",time());
        $u_id=Cookie::get('uid');
        $data=[
            'type'=>$type,
            'out_trade_no'=>$out_trade_no,
            'addtime'=>$addtime,
            'u_id'=>$u_id,
            'amount'=>$amount
        ];
        if ($result = self::create($data)) {
            $o_id=$result->o_id;
            return array(
                'o_id'=>$o_id,
                'out_trade_no'=>$out_trade_no,
            );
        } else {
            return $result->getError();
        }
    }


    /*
     * 作者：刘志祥
     * 板块：现场测试
     */
    public function nowTest($id ,$price){
        $indent = self :: createuniquenumber();
        if($price == 0){
            $data['is_pay'] =1;
        }
        $data['type'] = 4;
        $data['out_trade_no'] = $indent;
        $data['addtime'] = date("Y-m-d H:i:s");
        $data['u_id'] = $id;
        $data['amount'] = $price;
        return Db::table('order')->insertGetId($data);
    }
}