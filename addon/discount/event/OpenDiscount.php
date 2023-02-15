<?php
namespace addon\discount\event;
use addon\discount\model\Discount;
/**
 * 启动活动
 */
class OpenDiscount
{
    public function handle($params)
    {
        $discount = new Discount();
        $res      = $discount->cronOpenDiscount($params['relate_id']);
        return $res;
    }
}