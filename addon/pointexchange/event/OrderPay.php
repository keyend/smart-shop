<?php
namespace addon\pointexchange\event;
use addon\pointexchange\model\Order;
/**
 * 活动展示
 */
class OrderPay
{
	
	/**
	 * 活动展示
	 * @return multitype:number unknown
	 */
	public function handle($param)
	{
		if ($param['promotion_type'] == 'pointexchange') {
			$order_model = new Order();
			$order_info_result = $order_model->getOrderInfo([ [ 'relate_order_id', '=', $param['order_id'] ] ]);
			if ($order_info_result['code'] < 0) {
				return $order_info_result;
			}
			$res = $order_model->orderPay($order_info_result['data']);
			return $res;
		}
	}
}