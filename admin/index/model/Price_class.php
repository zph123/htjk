<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Price_class extends Model
{
    //搜索数据
    public function alls(){
        $data= Db::table('price_class')->select();
        return $data;
    }
    //修改数值
    public function updates($p_id,$p_price)
    {
         $data= Db::table('price_class')->where('p_id',$p_id)->update(['p_price'=>$p_price]);
       return $data;
    }

}
?>