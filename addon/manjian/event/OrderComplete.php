<?php
namespace addon\manjian\event;
use addon\manjian\model\Order;
/**
 * 订单完成
 */
class OrderComplete
{
    public function handle($params)
    {
        if (isset($params['order_id'])) {
            $order = new Order();
            $order->orderComplete($params['order_id']);
        }
    }
}