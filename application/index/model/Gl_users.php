<?php
namespace app\index\model;
use think\Model;
use think\Db;
class Gl_users extends Model
{
	//添加账号
	function add_one($data)
	{
		$data['password'] = md5($data['password']);
		$data['signuptime'] = date('Y-m-d H:i:s');
		$id = Db::table('gl_users')->insertGetId($data);
		return $id;
	}
	//验证唯一
	function check_one($name){
		$data = Db::table('gl_users')->where($name)->select();
		if($data){
			return true;
		}else{
			return false;
		}
	}
}