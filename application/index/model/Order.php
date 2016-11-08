<?php
namespace app\index\model;


use think\Model;
use think\Db;
use think\Session;

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
        $u_id=session::get('uid');
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
}