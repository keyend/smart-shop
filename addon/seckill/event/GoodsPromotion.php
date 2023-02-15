<?php
namespace addon\seckill\event;
use addon\seckill\model\Seckill;
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
            if (!empty($promotion_addon['seckill'])) {
                $seckill_model = new Seckill();
                $condition     = [
                    ['sg.seckill_id', '=', $promotion_addon['seckill']],
                    ['sku.sku_id', '=', $param['sku_id']]
                ];
                $field         = 'sku.goods_id,sku.sku_id,sku.price,sku.site_id,sg.seckill_id,sg.id,ps.seckill_start_time,ps.seckill_end_time';
                $goods_detail  = $seckill_model->getSeckillGoodsDetail($condition, $field);
                $goods_detail  = $goods_detail['data'];
                if (!empty($goods_detail)) {
                    $time = time() - strtotime(date("Y-m-d"),time());
                    if ($time > $goods_detail['seckill_start_time'] && $time < $goods_detail['seckill_end_time']) {
                        $goods_detail['promotion_type'] = 'seckill';
                        $goods_detail['promotion_name'] = '限时秒杀';
                        return $goods_detail;
                    }
                }
            }
        }
        return [];
    }
}