<?php
namespace app\index\model;
use think\Model;
use think\Db;
class user_infos extends Model
{
	//添加账号
	function add_one($data)
	{
		$id = Db::table('user_infos')->insert($data);
		return $id;
	}
	//验证唯一
	function check_one($name){
		$data = Db::table('user_infos')->where($name)->select();
		if($data){
			return true;
		}else{
			return false;
		}
	}
}