<?php
namespace app\event;
use app\model\order\OrderCommon;
/**
 * 订单异步回调执行
 */
class OrderPayNotify
{
    // 行为扩展的执行入口必须是run
    public function handle($data)
    {
        $order = new OrderCommon();
        $order->orderOnlinePay($data);
    }
}