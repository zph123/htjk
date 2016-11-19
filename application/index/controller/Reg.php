<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Gl_users;
use app\index\model\user_infos;

header("content-type:text/html;charset=utf-8");
class Reg extends Controller
{
	//跳转到注册页面
	public function index(){
		return view('login/reg');
	}
	//第二次数据过滤，谨防非法数据
	public function add_filter(){
        $infos['name']=input('customer');
        $infos['contact_phone']=input('contact_phone');
        $infos['password']=input('password');
        $infos['comfirm_password']=input('comfirm_password');
        $infos['fullname']=input('name');
        $infos['gender']=input('gender');
        $infos['birthday']=input('appDate');
        $infos['id_number']=input('id_number');
        $infos['birth_height']=input('birth_height');
        $infos['birth_weight']=input('birth_weight');
        $infos['birth_smoothly']=input('birth_smoothly');
        $infos['father_height']=input('father_height');
        $infos['mother_height']=input('mother_height');
        $infos['contact_address']=input('contact_address');
        $infos['email']=input('email');
        $infos['school']=input('school');

        $state=1;
        //用户名
        if($this->check_name($infos['name'])!=2)$state=0;
        //手机号
        if($this->check_phone($infos['contact_phone'])!=2)$state=0;
        //密码
        if(strlen($infos['password'])<6||strlen($infos['password'])>12||$infos['password']!=$infos['comfirm_password'])$state=0;
        //全名
        $pattern_fullname="/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{2,8}$/u";
        if(!preg_match($pattern_fullname,$infos['fullname']))$state=0;
        //性别
        if($infos['gender']==0||$infos['gender']==1);else $state=0;
        //生日
        $year=substr($infos['birthday'],0,4);
        $month=ltrim(substr($infos['birthday'],5,2),'0');
        $day=ltrim(substr($infos['birthday'],8),'0');
        if(!checkdate($month,$day,$year))$state=0;
        //身份证
        if($this->check_id_card($infos['id_number'])!=2)$state=0;
        //检测身份证与生日差值
        if(!$this->check_idcard_birthday(
            $infos['birthday'],
            $infos['id_number']
        ))$state=0;
        //出生身高
        if($infos['birth_height']<=0||$infos['birth_height']>100)$state=0;
        //出生体重
        if($infos['birth_weight']<=0||$infos['birth_weight']>20)$state=0;
        //是否顺产
        if($infos['birth_smoothly']||$infos['birth_smoothly']);else $state=0;
        //父亲身高
        if($infos['father_height']<=0||$infos['father_height']>300)$state=0;
        //母亲身高
        if($infos['mother_height']<=0||$infos['mother_height']>300)$state=0;
        //联系地址
        $pattern_address="/^[\x{4e00}-\x{9fa5}a-zA-Z][\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u";
        if(!preg_match($pattern_address,$infos['contact_address']))$state=0;
        //邮箱
//        if(!empty($infos['email'])){
            $pattern_email="/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]+$/";
            if (!preg_match($pattern_email,$infos['email']))$state=0;
//        }
        if(empty($infos['school']))$state=0;


        if(!$state)return "非法数据提交！";


        //因为数据库的冗余设计，进行数据复制及分割
        $infos_1['name']=$infos['name'];
        $infos_1['password']=$infos['password'];
        $infos_1['fullname']=$infos['fullname'];
        $infos_1['sex']=$infos['gender'];
        $infos_1['year']=$year.'-'.$month.'-'.$day;
        $infos_1['school']=$infos['school'];
        $infos_1['phone']=$infos['contact_phone'];

        $infos_2['gender']=$infos['gender'];
        $infos_2['birthday']=$infos_1['year'];
        $infos_2['id_number']=$infos['id_number'];
        $infos_2['birth_height']=$infos['birth_height'];
        $infos_2['birth_weight']=$infos['birth_weight'];
        $infos_2['birth_smoothly']=$infos['birth_smoothly'];
        $infos_2['father_height']=$infos['father_height'];
        $infos_2['mother_height']=$infos['mother_height'];
        $infos_2['contact_address']=$infos['contact_address'];
        $infos_2['email']=$infos['email'];
        $infos_2['school']=$infos['school'];

        $this->add($infos_1,$infos_2);
	}


	private function add($infos_1,$infos_2){
        //添加入库
        $user = new Gl_users();
        $u_id=$user->add_one($infos_1);

        $infos_2['u_id']=$u_id;
        $info_table=new user_infos();
        $info_table->add_one($infos_2);


        $this->redirect('Login/index');
    }

    //ajax验证转接口
	public function ajax_check(){
        $data = input('sign');
        $data_name = input('data_name');
        if($data_name=='name')echo $this->check_name($data);
        elseif($data_name=='phone')echo $this->check_phone($data);
        elseif($data_name=='id_number')echo $this->check_id_card($data);
    }
    //ajax验证身份证与生日差值
    public function ajax_check_birth_idcard(){
        $birth=input('birth');
        $idcard=input('idcard');
        if($this->check_idcard_birthday($birth,$idcard))
            echo 1;
        else echo 0;
    }
	//用户名检查
	protected function check_name($info){
        $pattern_name="/^[\x{4e00}-\x{9fa5}A-Za-z][\x{4e00}-\x{9fa5}A-Za-z0-9_-]{1,8}$/u";
//        var_dump($name);die;
        if(preg_match($pattern_name,$info)){
            $user = new Gl_users();
            $stauts = $user->check_one(['name'=>$info]);
            if($stauts){
                return 1;
            }else{
                return 2;
            }
        }else return 0;
    }
	//手机号检查
	protected function check_phone($info){
        $pattern_phone="/^1[3|4|5|7|8][0-9]\d{8}$/";
        if(preg_match($pattern_phone,$info)){
            $user = new Gl_users();
            $stauts = $user->check_one(['phone'=>$info]);
            if($stauts){
                return 1;
            }else{
                return 2;
            }
        }else return 0;
	}
    //身份证号检查
    protected function check_id_card($info){
        $check_res=checkIdCard($info);
        if($check_res['state']){
            $user = new user_infos();
            $stauts = $user->check_one(['id_number'=>$info]);
            if($stauts){
                return 1;
            }else{
                return 2;
            }
        }else return 0;
    }
    /**检测身份证生日与输入的生日的差值：
     * @param string $birthday 生日
     * @param string $id_card 身份证号码
     * @return bool 如果相差大于3个平年返回false，否则返回true
     */
    protected function check_idcard_birthday($birthday,$id_card){

        if(empty($birthday)||empty($id_card))return false;
        //生日
        $year1=substr($birthday,0,4);
        $month1=ltrim(substr($birthday,5,2),'0');
        $day1=ltrim(substr($birthday,8),'0');
        if(!checkdate($month1,$day1,$year1))return false;
        $date_birth=$year1.'-'.$month1.'-'.$day1;
        //获取身份证号年月日期
        $check_res=checkIdCard($id_card);
        if(!$check_res['state'])return false;
        $the_date=substr($id_card,6,8);
        $year2=substr($the_date,0,4);
        $month2=ltrim(substr($the_date,4,2),'0');
        $day2=ltrim(substr($the_date,6,2),'0');
        $date_idcard=$year2.'-'.$month2.'-'.$day2;
        //检验
        $date_1=new \DateTime($date_birth);
        $date_2=new \DateTime($date_idcard);

        $date_diff=$date_1->diff($date_2);
        $days_diff=abs($date_diff->format("%R%a"));
        if($days_diff>365*3)return false;
        else return true;
    }
}
