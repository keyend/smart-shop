<?php
namespace addon\coupon\event;
use addon\coupon\model\CouponType;
/**
 * 优惠券定时结束
 */
class CronCouponTypeEnd
{
    public function handle($params = [])
    {
        $coupon = new CouponType();
        $res    = $coupon->couponCronEnd($params['relate_id']);
        return $res;
    }
}