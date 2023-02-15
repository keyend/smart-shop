<?php
namespace addon\pointexchange\event;
use addon\pointexchange\model\Order;
/**
 * 订单关闭
 */
class OrderClose
{
	
	/**
	 * 订单关闭
	 * @return multitype:number unknown
	 */
	public function handle($param)
	{
        $order_model = new Order();
        $order_info_result = $order_model->getOrderInfo([ [ 'relate_order_id', '=', $param['order_id'] ] ]);
        if ($order_info_result['code'] < 0) {
            return $order_info_result;
        }
        $res = $order_model->closeOrder($order_info_result['data']['order_id']);
        return $res;
	}
}