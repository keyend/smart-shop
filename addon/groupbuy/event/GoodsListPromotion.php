<?php
namespace addon\groupbuy\event;
use addon\groupbuy\model\Groupbuy;
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
        if (empty($param['promotion']) || $param['promotion'] != 'groupbuy') return [];
        $condition[] = [
            ['pg.site_id', '=', $param['site_id']],
            ['pg.status', '=', 2]
        ];
        if (!empty($param['goods_name'])) {
            $condition[] = ['pg.goods_name', 'like', '%' . $param['goods_name'] . '%'];
        }
        $groupbuy_model = new Groupbuy();
        $list           = $groupbuy_model->getGroupbuyGoodsPageList($condition, $param['page'], $param['page_size'], 'pg.groupbuy_id desc');
        return $list;
    }
}