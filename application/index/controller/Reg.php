<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Gl_users;

header("content-type:text/html;charset=utf-8");
class Reg extends Controller
{
	//跳转到注册页面
	public function index(){
		return view('login/reg');
	}
	//添加账号
	public function add(){
        //验证用户名
        $name=Request::instance()->post('name');
        $uname=Db::table('gl_users')->where('name', $name)->find();
        $namematch = '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{2,8}$/u';
        if(!preg_match($namematch,$name)){
            echo '用户名格式不正确';die;
        }else if($uname!=""){
            echo '该用户名已经被注册，请重新输入';die;
        }

        //验证日期
        $year=Request::instance()->post('year');
        if($year==""){
            echo '日期不能为空';die;
        }
        $reg4="/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";
        if(!preg_match($reg4,$year)){
            echo '日期格式不正确';die;
        }

        //验证手机号格式
        $phone=Request::instance()->post('phone');
        if($phone==""){
            echo '手机号码不能为空';die;
        }
        $reg3="/^1[358]\d{9}$/";
        if(!preg_match($reg3,$phone)){
            echo '手机号码格式不正确';die;
        }

        //验证密码
        $password=Request::instance()->post('password');
        if($password==""){
            echo '密码不能为空';die;
        }
        if(strlen($password)<6 || strlen($password)>12){
            echo '密码格式不正确';die;
        }
        $comfirm_password = Request::instance()->post('comfirm_password');
        if($password!=$comfirm_password){
            echo '两次密码不相同';die;
        }

        //添加入库
        $data = Request::instance()->post();
		$user = new Gl_users();
    	$user->add_one($data);
        $this->redirect('Login/index');
	}
	//验证姓名唯一
	public function check_only(){
        $name = $_GET;
//		$name = Request::instance()->post();
//        var_dump($name);die;
		$user = new Gl_users();
		$stauts = $user->check_one($name);
		if($stauts){
			echo 1;
		}else{
			echo 0;
		}
	}
}
