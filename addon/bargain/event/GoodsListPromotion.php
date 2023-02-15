<?php
namespace addon\bargain\event;
use addon\bargain\model\Bargain;
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
        if (empty($param['promotion']) || $param['promotion'] != 'bargain') return [];
        $condition = [
            ['pg.site_id', '=', $param['site_id']],
            ['pg.status', '=', 1]
        ];
        if (isset($param['bargain_name']) && !empty($param['bargain_name'])) {
            $condition[] = ['pg.bargain_name', 'like', '%' . $param['bargain_name'] . '%'];
        }
        $ailas = 'pg';
        $join  = [
            ['goods_sku sku', 'pg.sku_id = sku.sku_id', 'inner'],
        ];
        $field = 'pg.id,pg.bargain_id,pg.floor_price,pg.bargain_stock,pg.site_id,pg.bargain_name,sku.sku_id,sku.price,sku.sku_name,sku.sku_image,sku.stock as goods_stock';
        $model = new Bargain();
        $list  = $model->getBargainGoodsPageList($condition, $field, 'id asc', $param['page'], $param['page_size'], $ailas, $join);
        return $list;
    }
}