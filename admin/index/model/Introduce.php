<?php
namespace app\index\model;
use think\Model;
use think\Db;
use \think\db\Query;
class Introduce extends Model
{
    public function setup_add($arr){
        $id = Db::table('introduce')->insert($arr);
        return $id;
    }
}