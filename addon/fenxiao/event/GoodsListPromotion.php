<?php
namespace addon\fenxiao\event;
use app\model\goods\Goods as GoodsModel;

/**
 * 商品营销活动信息
 */
class GoodsListPromotion
{

    /**
     * 商品营销活动信息
     * @param $param
     * @return array
     */
    public function handle($param)
    {
        if (empty($param[ 'promotion' ]) || $param[ 'promotion' ] != 'fenxiao') return [];

        $condition = [
            [ 'is_delete', '=', 0 ],
            [ 'site_id', '=', $param[ 'site_id' ] ],
            [ 'is_fenxiao', '=', 1 ],
            [ 'goods_state', '=', 1 ]
        ];
        if (!empty($param[ 'goods_name' ])) {
            $condition[] = [ 'goods_name', 'like', '%' . $param[ 'goods_name' ] . '%' ];
        }
        if (!empty($param[ 'category_id' ])) {
            $condition[] = [ 'category_id', 'like', '%,' . $param[ 'category_id' ] . ',%' ];
        }
        $model = new GoodsModel();
        $list = $model->getGoodsPageList($condition, $param[ 'page' ], $param[ 'page_size' ]);
        return $list;
    }
}