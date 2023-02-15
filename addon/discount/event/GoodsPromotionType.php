<?php
namespace addon\discount\event;
/**
 * 活动类型
 */
class GoodsPromotionType
{
    /**
     * 活动类型
     * @return multitype:number unknown
     */
    public function handle()
    {
        return ["name" => "限时折扣", "short" => "折", "type" => "discount", "color" => "#22AFB9", 'url' => 'discount://shop/discount/lists'];
    }
}