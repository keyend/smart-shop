<?php
namespace addon\topic\event;
/**
 * 订单营销活动类型
 */
class OrderPromotionType
{
    /**
     * 订单营销活动类型
     * @return multitype:number unknown
     */
    public function handle()
    {
        return ["name" => "专题", "type" => "topic"];
    }
}