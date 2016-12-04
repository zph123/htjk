<?php
namespace app\index\model;
use think\Model;
use think\Db;
use \think\db\Query;
class Article extends Model
{
    /**获取整表 作者：李斌
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function count_category(){
        return Db::table('category_article')
            ->select()
            ;
    }

    /**分类配合即点及改 作者：李斌
     * @param $data
     * @return int
     */
    public function category_update($data)
    {
        return Db::table('category_article')
               ->where('c_id',$data['c_id'])
               ->setField('c_name',$data['c_name']);
    }

    /**添加分类 作者：李斌
     * @param $data
     * @return int|string
     */
    public function c_add_data($data)
    {
        return Db::table('category_article')
            ->insertGetId($data)
            ;
    }

    /**检定分类下是否有文章 作者：李斌
     * @param $c_id
     * @return bool
     */
    public function check_articles($c_id){
        $res=Db::table('article')
            ->where('c_id','=',$c_id)
            ->select();
        if(empty($res))return false;
        else return true;
    }
    //删除分类
    public function delete_category($c_id){
        $res=Db::table('category_article')
            ->where('c_id','=',$c_id)
            ->delete();
        if($res)return true;
        else return false;
    }
    //删除分类下文章
    public function delete_c_articles($c_id){
        $res=Db::table('article')
            ->where('c_id','=',$c_id)
            ->delete();
        if($res)return true;
        else return false;
    }
}