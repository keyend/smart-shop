<?php
namespace addon\groupbuy\event;
use addon\groupbuy\model\Groupbuy;
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
        if (empty($param['goods_id'])) return [];
        $goods_model = new GoodsModel();
        $goods_info  = $goods_model->getGoodsInfo([['goods_id', '=', $param['goods_id']]], 'promotion_addon');
        $goods_info  = $goods_info['data'];
        if (!empty($goods_info['promotion_addon'])) {
            $promotion_addon = json_decode($goods_info['promotion_addon'], true);
            if (!empty($promotion_addon['groupbuy'])) {
                $groupbuy_model = new Groupbuy();
                $condition      = [
                    ['groupbuy_id', '=', $promotion_addon['groupbuy']],
                    ['goods_id', '=', $param['goods_id']],
                    ['status', '=', 2]
                ];
                $goods_detail   = $groupbuy_model->getGroupbuyInfo($condition, 'groupbuy_id,site_id,goods_id,goods_price,groupbuy_price,sku_id');
                $goods_detail   = $goods_detail['data'];
                if (!empty($goods_detail)) {
                    $goods_detail['promotion_type'] = 'groupbuy';
                    $goods_detail['promotion_name'] = '团购';
                    return $goods_detail;
                }
            }
        }
        return [];
    }
}