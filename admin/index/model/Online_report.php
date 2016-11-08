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
        $sql="select * from online_report join onlinetest on onlinetest.register_id=online_report.type_id where $where";
        return Db::query($sql);
    }
    /*
    *现场测试报告展示
    */
    public function now_show($where){
        $sql="select * from online_report join nowtest on nowtest.n_id=online_report.type_id where $where";
        return Db::query($sql);
    }
}