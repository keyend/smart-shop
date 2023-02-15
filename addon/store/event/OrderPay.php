<?php
namespace addon\store\event;
use addon\store\model\StoreOrder;
/**
 * 添加下单用户为门店用户
 */
class OrderPay
{
    public function handle($order)
    {
        $store_order = new StoreOrder();
        $res         = $store_order->orderPay($order);
        return $res;
    }
}