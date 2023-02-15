<?php
namespace addon\pointexchange\api\controller;
use app\api\controller\BaseApi;
use addon\pointexchange\model\Exchange as ExchangeModel;
/**
 * 积分兑换
 */
class Goods extends BaseApi
{
	/**
	 * 详情信息
	 */
	public function detail()
	{
		$id = isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : 0;
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		$exchange_model = new ExchangeModel();
		$info = $exchange_model->getExchangeGoodsDetail($id, $this->site_id);
		return $this->response($info);
	}
	public function page()
	{
		$page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
		$page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
		$type = isset($this->params[ 'type' ]) ? $this->params[ 'type' ] : 1;//兑换类型，1：商品，2：优惠券，3：红包
		$condition = [
			[ 'pe.state', '=', 1 ],
			[ 'pe.type', '=', $type ],
			[ 'pe.site_id', '=', $this->site_id ],
		];
		$order = 'pe.create_time desc';
		$field = 'pe.id,pe.type,pe.type_name,pe.type_id,pe.name,pe.image,pe.stock,pe.point,pe.market_price,pe.price,pe.delivery_price,pe.balance';
		$alias = 'pe';
		$join = [];
		if ($type == 1) {
			$join = [
				[ 'goods_sku gs', 'pe.type_id = gs.sku_id', 'inner' ]
			];
		} elseif ($type == 2) {
			$join = [
				[ 'promotion_coupon_type pct', 'pe.type_id = pct.coupon_type_id', 'inner' ]
			];
		}
		$exchange_model = new ExchangeModel();
		$list = $exchange_model->getExchangePageList($condition, $page, $page_size, $order, $field, $alias, $join);
		return $this->response($list);
	}
}