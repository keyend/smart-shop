<?php
namespace addon\bundling\event;
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
        return ["name" => "组合套餐", "type" => "bundling"];
    }
}