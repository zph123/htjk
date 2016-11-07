<?php 
namespace app\index\model;
use think\Model;
use think\Db;
class Localetest extends Model
{
	/**
	 * 现场测试数据查询
	 * 已经支付未生成测试报告的数据
	 */
	public function on_select($where,$start)
	{
 		
		return Db::table('nowtest')->where($where)->where('n_status',1)->limit($start,5)->select();
	}

	public function two_select($where)
	{	
		return Db::table('nowtest')->where($where)->where('n_status',1)->select();
	}


	/**
	 * 现场试报告
	 */
	public function now_test($where,$start)
	{
 		
		return Db::table('nowtest')->where($where)->where('n_status',2)->limit($start,5)->select();
	}

	public function now_select($where)
	{	
		return Db::table('nowtest')->where($where)->where('n_status',2)->select();
	}

	/**
	 * 数据详情查询
	 */
	public function one_select($id)
	{
      return Db::table('nowtest')->where('n_id',$id)->find();

	}

	/*
	*修改测试报告状态为已生成
	*/
	public function update_nowtest($n_id)
	{
		return Db::table('nowtest')->where('n_id',$n_id)->setField('n_status',2);
	}		
}