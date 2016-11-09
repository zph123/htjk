<?php

namespace app\index\controller;
use think\Db;
use think\Controller;
use think\Session;
use think\Request;

class Cost extends Controller
{
    /**作者：李斌
     * 显示用户本月消费列表
     * @return \think\Response
     */
    public function cost_list()
    {
        $u_id=Session::get('uid');
        $u_id=456;
        $date=date('Y-m-01', strtotime(date("Ymd",time())));

        $cost_list=Db::name('order')
            ->where('status','=','1')
            ->where('addtime','>=',$date)
            ->where('u_id','=',$u_id)
            ->limit(10)
            ->column('o_id,type,addtime,amount')
        ;
        $price_class=Db::name('price_class')
            ->column('p_id,p_name')
        ;
//        var_dump($price_class);die;
        return view('cost_list',[
            'cost_list'=>$cost_list,
            'price_class'=>$price_class
        ]);
    }

}
