<?php
namespace addon\topic\event;
use addon\topic\model\TopicGoods;
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
            if (!empty($promotion_addon['topic'])) {
                $topic_model  = new TopicGoods();
                $condition    = [
                    ['pt.topic_id', '=', $promotion_addon['topic']],
                    ['sku.sku_id', '=', $param['sku_id']],
                    ['pt.status', '=', 2]
                ];
                $field        = 'sku.goods_id,sku.sku_id,sku.price,sku.site_id,ptg.id,ptg.topic_id,ptg.topic_price,pt.topic_name';
                $goods_detail = $topic_model->getTopicGoodsDetail($condition, $field);
                $goods_detail = $goods_detail['data'];
                if (!empty($goods_detail)) {
                    $goods_detail['promotion_type'] = 'topic';
                    $goods_detail['promotion_name'] = '专题活动';
                    return $goods_detail;
                }
            }
        }
        return [];
    }
}