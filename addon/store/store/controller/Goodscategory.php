<?php
namespace addon\store\store\controller;
use app\model\goods\GoodsCategory as GoodsCategoryModel;
/**
 * 商品分类管理 控制器
 */
class Goodscategory extends BaseStore
{
    /**
     * 获取商品分类
     * @return \multitype
     */
    public function getCategoryByParent()
    {
        $pid                  = input('pid', 0);// 上级id
        $level                = input('level', 0);// 层级
        $goods_category_model = new GoodsCategoryModel();
        if (!empty($level)) {
            $condition[] = ['level', '=', $level];
        }
        if (!empty($pid)) {
            $condition[] = ['pid', '=', $pid];
        }
        $condition[] = ['site_id', '=', $this->site_id];
        $list        = $goods_category_list = $goods_category_model->getCategoryByParent($condition, 'category_id,category_name,pid,level,category_id_1,category_id_2,category_id_3');
        return $list;
    }
}