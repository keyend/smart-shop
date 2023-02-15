<?php
namespace addon\memberprice\event;
/**
 * 活动类型
 */
class PromotionType
{
    /**
     * 活动类型
     * @return multitype:number unknown
     */
    public function handle()
    {
        return ["name" => "会员价", "type" => "memberprice"];
    }
}