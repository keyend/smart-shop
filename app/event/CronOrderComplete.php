<?php
namespace app\event;
use app\model\order\OrderCommon;
/**
 * 订单自动完成
 */
class CronOrderComplete
{
    // 行为扩展的执行入口必须是run
    public function handle($data)
    {
        $order = new OrderCommon();
        $res   = $order->orderComplete($data["relate_id"]);//订单自动完成
        return $res;
    }
}