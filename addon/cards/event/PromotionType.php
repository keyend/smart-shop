<?php
namespace addon\cards\event;
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
        return ["name" => "刮刮乐", "type" => "cards"];
    }
}