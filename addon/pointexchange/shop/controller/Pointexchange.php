<?php
namespace addon\Pointexchange\shop\controller;
use addon\pointexchange\model\Order as ExchangeOrderModel;
use app\shop\controller\BaseShop;
/**
 * 兑换发放订单
 */
class Pointexchange extends BaseShop
{
	
	/**
	 * 兑换订单列表
	 * @return mixed
	 */
	public function lists()
	{
		
		$exchange_id = input('exchange_id', '');
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');
			$condition = [];
			if ($search_text) {
				$condition[] = [ 'exchange_name', 'like', '%' . $search_text . '%' ];
			}
			
			$type = input('type', '');
			if ($type) {
				$condition[] = [ 'type', '=', $type ];
			}
			
			if ($exchange_id) {
				$condition[] = [ 'exchange_id', '=', $exchange_id ];
			}
			
			$order = 'create_time desc';
			$field = '*';
			
			$exchange_order_model = new ExchangeOrderModel();
			return $exchange_order_model->getExchangePageList($condition, $page, $page_size, $order, $field);
		} else {
			$this->assign('exchange_id', $exchange_id);
			$this->forthMenu();
			return $this->fetch("exchange_order/lists");
		}
		
	}
	
	/**订单详情
	 * @return mixed
	 */
	public function detail()
	{
		$order_id = input('order_id', 0);
		$order_model = new ExchangeOrderModel();
		$order_info = $order_model->getOrderInfo([ [ 'order_id', '=', $order_id ] ]);
		$order_info = $order_info["data"];
		$this->assign("order_info", $order_info);
		return $this->fetch('exchange_order/detail');
	}
}