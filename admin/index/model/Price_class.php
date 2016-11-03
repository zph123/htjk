<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Price_class extends Model
{
    function updates($p_id,$p_price)
    {
         $data= Db::table('price_class')->where('p_id',$p_id)->update(['p_price'=>$p_price]);
       return $data;
    }

}
?>