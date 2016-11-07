<?php
namespace app\index\model;
use think\Model;
use think\Db;
use \think\db\Query;
class Online_report extends Model
{
    /*
    *在线测试报告数据填充
    */
    public function report_add($arr){
        $id = Db::table('online_report')->insert($arr);
        return $id;
    }
    /*
    *测试报告展示
    */
    public function report_show($where){
        $sql="select * from online_report join onlinetest on onlinetest.register_id=online_report.or_id where $where";
        return Db::query($sql);
    }
}