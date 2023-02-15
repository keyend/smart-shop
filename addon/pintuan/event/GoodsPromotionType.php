<?php
namespace addon\pintuan\event;
/**
 * 活动类型
 */
class GoodsPromotionType
{
    /**
     * 活动类型
     * @return multitype:number unknown
     */
    public function handle()
    {
        return ["name" => "拼团", "short" => "拼", "type" => "pintuan", "color" => "#F58760", 'url' => 'pintuan://shop/pintuan/lists'];
    }
}