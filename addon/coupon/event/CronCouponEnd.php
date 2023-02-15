<?php
namespace addon\coupon\event;
use addon\coupon\model\Coupon;
/**
 * 启动活动
 */
class CronCouponEnd
{
    public function handle($params = [])
    {
        $coupon = new Coupon();
        $res    = $coupon->cronCouponEnd();
        return $res;
    }
}