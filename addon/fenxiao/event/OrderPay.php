<?php
namespace addon\fenxiao\event;
use addon\fenxiao\model\FenxiaoOrder;
/**
 * 活动展示
 */
class OrderPay
{

    /**
     * 订单结算
     * @param unknown $order
     * @return multitype:
     */
    public function handle($order)
    {
        $fenxiao_order = new FenxiaoOrder();
        $res           = $fenxiao_order->calculate($order);
        return $res;
    }
}