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
}