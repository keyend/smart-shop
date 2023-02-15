<?php
namespace addon\agencyteam\event;
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
        return ["name" => "代理团队", "type" => "agencyteam"];
    }
}