<?php
namespace addon\bundling\event;
/**
 * 活动
 */
class PromotionType
{
    /**
     * 活动类型
     * @return multitype:number unknown
     */
    public function handle()
    {
        return ["name" => "组合套餐", "type" => "bunding"];
    }
}