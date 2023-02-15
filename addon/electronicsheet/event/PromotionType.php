<?php
namespace addon\electronicsheet\event;
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
        return ["name" => "电子面单", "type" => "electronicsheet"];
    }
}