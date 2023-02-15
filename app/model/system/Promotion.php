<?php
namespace app\model\system;
use app\model\BaseModel;
/**
 * 活动整体管理
 */
class Promotion extends BaseModel
{
    /**
     * 获取营销活动展示
     */
    public function getPromotions()
    {
        $show           = event("ShowPromotion", []);
        $shop_promotion = [];
        foreach ($show as $k => $v) {
            if (!empty($v['shop'])) {
                $shop_promotion = array_merge($shop_promotion, $v['shop']);
            }
        }
        return [
            'shop' => $shop_promotion
        ];
    }
    /**
     * 获取站点营销活动展示
     * @param $site_id
     */
    public function getSitePromotions($site_id)
    {
        $show      = event("ShowPromotion", []);
        $promotion = [];
        foreach ($show as $k => $v) {
            if (!empty($v['shop'])) {
                $promotion = array_merge($promotion, $v['shop']);
            }
        }
        return $promotion;
    }
    /**
     * 获取营销类型
     */
    public function getPromotionType()
    {
        $promotion_type   = event("PromotionType");
        $promotion_type[] = ["type" => "empty", "name" => "无营销活动"];
        return $promotion_type;
    }
}