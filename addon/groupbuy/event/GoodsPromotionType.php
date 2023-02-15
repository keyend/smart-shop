<?php
namespace addon\groupbuy\event;
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
        return ["name" => "团购", "short" => "团", "type" => "groupbuy", "color" => "#4CB130", 'url' => 'groupbuy://shop/groupbuy/lists'];
    }
}