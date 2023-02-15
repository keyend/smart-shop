<?php
namespace addon\membercancel\event;
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
        return ["name" => "会员注销", "type" => "membercancel"];
    }
}