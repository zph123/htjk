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
	// 查询单条详情
	function user_find($table,$where){
		$data = Db::table("$table")->where('id',$where)->find();
		return $data;
	}
}