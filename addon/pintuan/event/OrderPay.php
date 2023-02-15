<?php
namespace addon\pintuan\event;
use addon\pintuan\model\PintuanOrder;
/**
 * 活动展示
 */
class OrderPay
{
    /**
     * 活动展示
     *
     * @return multitype:number unknown
     */
    public function handle($param)
    {
        if ($param['promotion_type'] == 'pintuan') {
            $pintuan_order = new PintuanOrder();
            $res           = $pintuan_order->orderPay($param);
            return $res;
        }
    }
}