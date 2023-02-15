<?php
namespace addon\bargain\event;
use addon\pintuan\model\Pintuan;
use app\model\goods\Goods as GoodsModel;
/**
 * 商品营销活动信息
 */
class GoodsPromotion
{
    /**
     * 商品营销活动信息
     * @param $param
     * @return array
     */
    public function handle($param)
    {
        if (empty($param['sku_id'])) return [];
        $goods_detail = model('promotion_bargain_goods')->getInfo([ ['sku_id', '=', $param['sku_id']], ['status', '=', 1] ], 'id');
        if (!empty($goods_detail)) {
            $goods_detail['promotion_type'] = 'bargain';
            $goods_detail['promotion_name'] = '砍价';
            return $goods_detail;
        }
        return [];
    }
}