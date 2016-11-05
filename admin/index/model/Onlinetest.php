<?php 
namespace app\index\model;
use think\Model;
use think\Db;
use \think\db\Query;
class Onlinetest extends Model
{
	 
	/**
	 * 在线测试数据查询
	 */
	public function on_select($where,$start)
	{
 		
		return Db::table('onlinetest')->where($where)->limit($start,5)->select();
	}

	public function two_select($where)
	{
 		
		return Db::table('onlinetest')->where($where)->select();
	}

	/**
	 * 数据详情查询
	 */
	public function one_select($id)
	{
      return Db::table('onlinetest')->where('register_id',$id)->find();

	}
}