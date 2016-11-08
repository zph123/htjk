<?php
namespace app\index\model;
use think\Model;
use think\Db;
class user_model extends Model
{
	/**
	 * 查询个人信息列表
	 * @param  [type] $table 表名
	 * @param  [type] $where 条件
	 * @param  string $field 要取出的字段
	 * @return [type]        [description]
	 */
	function user_select($table,$where,$field='*')
	{
		$data = Db::table("$table")
		->where('uid',$where)
		->column($field);
		return $data;
	}
	function user_order($id,$type,$field='*'){
		$data = Db::table('order')
		->where('u_id',$id)
		->where('type',$type)
		->column($field);
		return $data;
	}
	// 查询单条详情
	function user_find($table,$id,$uid){
		$data = Db::table("$table")
		->where('id',$id)
		->where('uid',$uid)
		->find();
		return $data;
	}
	/**
	 * 查询用户填写的运动数据
	 */
	function user_motion($table,$id,$field='*',$date){
		$data = Db::table("$table")
		->where('uid',$id)
		->where('time','between',[$date,date('Y-m-d', strtotime("$date +1 month -1 day"))])
		->column($field);
		return $data;
	}
	/**
	 * 添加数据入库
	 */
	function add_motion($table,$array){
		return Db::table("$table")->insert($array);
	}
}