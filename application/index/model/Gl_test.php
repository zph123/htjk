<?php
namespace app\index\model;
use think\Model;
use think\Db;
class Gl_test extends Model
{
    //添加账号
    function add_one($data)
    {
        $dat['l_id'] = $data['l_id'];//关联活动列表id
        $dat['uid'] = $data['uid'];//登录人id
        $dat['n_price'] = $data['l_price'];//总价钱（初始价钱+预测身高的价钱）
        $dat['n_sex'] = $data['gender'];//测试人性别
        $dat['n_name'] = $data['customer'];//测试人名字
        $dat['n_date'] = $data['appDate'];//测试人出生日期
        $dat['n_idd'] = $data['id_number'];//测试人身份证号
        $dat['n_age'] = $data['age'];//测试人年龄
        $dat['n_stature'] = $data['birth_height'];//测试人身高
        $dat['n_weight'] = $data['birth_weight'];//体重
        $dat['n_eutocia'] = $data['birht_smoothly'];//是否顺产出生
        $dat['n_gonacratia'] = $data['spermatorrhea'];//是否已遗精 or 初潮
        $dat['n_phone'] = $data['contact_phone'];//手机号
        $dat['n_email'] = $data['email'];//邮箱
        $dat['n_address'] = $data['contact_address'];//联系地址
        $dat['n_fstature'] = $data['father_height'];//父亲身高
        $dat['n_mstature'] = $data['mother_height'];//母亲身高
        $dat['n_paper'] = $data['need_report'];//纸质报告
        $dat['n_school'] = $data['school'];//测试人学校
        $dat['n_time'] = $data['n_time'];//测试时间
        $dat['n_height'] = $data['predict_height'];//预测身高状态
        $dat['o_id'] = $data['o_id'];//
        return $id = Db::table('nowtest')->insert($dat);
    }
}