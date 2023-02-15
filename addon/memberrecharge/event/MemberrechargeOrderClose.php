<?php
namespace addon\memberrecharge\event;
use addon\memberrecharge\model\MemberrechargeOrder;
/**
 * 订单支付回调
 */
class MemberrechargeOrderClose
{
    public function handle($params)
    {
        $order = new MemberrechargeOrder();
        $res   = $order->cronMemberRechargeOrderClose($params['relate_id']);
        return $res;
    }
}