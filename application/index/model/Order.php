<?php
namespace app\index\model;


use think\Model;
use think\Db;

class Order extends Model
{
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
}