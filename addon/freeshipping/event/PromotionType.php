<?php
namespace addon\freeshipping\event;
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
        return ["name" => "满额包邮", "type" => "freeshipping"];
    }
}