<?php
namespace app\index\model;
use think\Model;
use think\Db;
use \think\db\Query;
class Article extends Model
{
    /**获取整表
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function count_category(){
        return Db::table('category_article')
            ->select()
            ;
    }

    /**分类配合即点及改
     * @param $data
     * @return int
     */
    public function category_update($data)
    {
        return Db::table('category_article')
               ->where('c_id',$data['c_id'])
               ->setField('c_name',$data['c_name']);
    }

    /**
     * 联查分类展示文章
     */
    public function article_list(){

    }

    /**
     * 文章编辑事务
     */
    public function article_save($data){
        var_dump($this->save($data));die;
    }

    /**
     * 数据详情查询
     */
    public function one_select($id)
    {
        return Db::table('onlinetest')->where('o_id',$id)->find();
    }

}