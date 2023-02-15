<?php
namespace addon\memberrecharge\event;
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
        return ["name" => "充值礼包", "type" => "memberrecharge"];
    }
}