<?php
namespace addon\memberrecharge\event;
use addon\memberrecharge\model\MemberrechargeOrder;
/**
 * 充值订单回调
 */
class MemberrechargeOrderPayNotify
{
    public function handle($data)
    {
        $model = new MemberRechargeOrder();
        $res   = $model->orderPay($data);
        return $res;
    }
}