<?php
namespace addon\pointexchange\event;
use addon\pointexchange\model\Order;
/**
 * 积分兑换订单关闭
 */
class CronExchangeOrderClose
{
    
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        $order = new Order();
        $order->closeOrder($data["relate_id"]);
	}
	
}