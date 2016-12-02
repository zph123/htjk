<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Cookie;
use app\index\model\Introduce;
header('content-type:text/html;charset=utf-8');
class Indexpage extends Common
{
	function setup()
	{
		return view('setup');
	}

	function setupadd(Request $request){

        $arr=Request::instance()->post();
        //print_r($arr);die;
        $img=$request->file('file_img');
        if(!empty($img)){
        	$size = $_FILES['file_img']['size'];         //获取图片大小
        	$type = $_FILES['file_img']['type']; 
        	$type= substr($type, strrpos($type, "/")+1);    //获取图片类型;
            $type_class= array('jpeg','png','gif');        //定义图片类型;

            //检测图片类型是否合格
            if(in_array($type,$type_class)){
						if($size<=1024*10){
							$Introduce = new Introduce();
				        	$Catalog_path=ROOT_PATH."public".DS."img";
				            is_dir($Catalog_path)or mkdir($Catalog_path,0777,true);
				            //将图片转移到 框架应用根目录/public/testimg/ 目录下
				            $file_info = $img->move($Catalog_path);
				            if ($file_info) {
				                $imgname=$file_info->getSaveName();
				                $arr['img_path'] = $imgname;
				                $res=$Introduce->setup_add($arr);
				                if($res){
			            	        $this->assign('error',"保存成功");
			                        return $this->fetch('setup');
				                }else{
			            	        $this->assign('error',"保存失败");
			                        return $this->fetch('setup');
				                }
				            } else {
				                // 上传失败获取错误信息，并显示
				                $this->error($img->getError());
				            }
						}else{
	            	        $this->assign('error',"图片超过限制大小");
	                        return $this->fetch('setup');							
						}
              } else {
            	        $this->assign('error',"请选择正确的图片类型");
                        return $this->fetch('setup');
              } 

        }else{
        $this->assign('error',"请选择图片上传");
        return $this->fetch('setup');
        }
	}	
}
