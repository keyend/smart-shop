<?php
namespace addon\turntable\event;
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
        return ["name" => "幸运抽奖", "type" => "turntable"];
    }
}