<?php
namespace addon\pointexchange\api\controller;
use app\api\controller\BaseApi;
use addon\pointexchange\model\OrderCreate as OrderCreateModel;
/**
 * 订单创建
 * @author Administrator
 *
 */
class Ordercreate extends BaseApi
{
	/**
	 * 创建订单
	 */
	public function create()
	{
		$token = $this->checkToken();
		if ($token[ 'code' ] < 0) return $this->response($token);
		$order_create = new OrderCreateModel();
		$data = [
			'id' => isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : '',//兑换id
			'num' => isset($this->params[ 'num' ]) ? $this->params[ 'num' ] : 1,//兑换数量(买几套)
			'member_id' => $this->member_id,
			'site_id' => $this->site_id,
			'is_balance' => intval($this->params[ 'is_balance' ]),
			'pay_password' => isset($this->params[ 'pay_password' ]) ? $this->params[ 'pay_password' ] : '',//支付密码
			'order_from' => $this->params[ 'app_type' ],
			'order_from_name' => $this->params[ 'app_type_name' ],
			'delivery' => isset($this->params[ "delivery" ]) && !empty($this->params[ "delivery" ]) ? json_decode($this->params[ "delivery" ], true) : [],
			'buyer_message' => $this->params[ 'buyer_message' ],
			'member_address' => isset($this->params[ "member_address" ]) && !empty($this->params[ "member_address" ]) ? json_decode($this->params[ "member_address" ], true) : [],
			'latitude' => $this->params[ "latitude" ] ?? null,
			'longitude' => $this->params[ "longitude" ] ?? null,
			'buyer_ask_delivery_time' => $this->params[ "buyer_ask_delivery_time" ] ?? '',
		];
		if (empty($data[ 'id' ])) {
			return $this->response($this->error('', '缺少必填参数商品数据'));
		}
		$res = $order_create->create($data);
		return $this->response($res);
	}
	/**
	 * 待支付订单 数据初始化
	 * @return string
	 */
	public function payment()
	{
		$token = $this->checkToken();
		if ($token[ 'code' ] < 0) return $this->response($token);
		$order_create = new OrderCreateModel();
		$data = [
			'id' => isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : '',//兑换id
			'num' => isset($this->params[ 'num' ]) ? $this->params[ 'num' ] : 1,//兑换数量(买几套)
			'is_balance' => 0,
			'site_id' => $this->site_id,//站点id
			'member_id' => $this->member_id,
			'order_from' => $this->params[ 'app_type' ],
			'order_from_name' => $this->params[ 'app_type_name' ],
			'latitude' => $this->params[ "latitude" ] ?? null,
			'longitude' => $this->params[ "longitude" ] ?? null,
			'buyer_ask_delivery_time' => $this->params[ "buyer_ask_delivery_time" ] ?? '',
                'default_store_id' => $this->params["default_store_id"] ?? 0,
		];
		if (empty($data[ 'id' ])) {
			return $this->response($this->error('', '缺少必填参数商品数据'));
		}
		$res = $order_create->payment($data);
		return $this->response($res);
	}
	public function calculate()
	{
		$token = $this->checkToken();
		if ($token[ 'code' ] < 0) return $this->response($token);
		$order_create = new OrderCreateModel();

		$data         							= [
			'id'											=> isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : '',//兑换id
			'num'             				=> isset($this->params['num']) ? $this->params['num'] : '',
			'site_id'         				=> $this->site_id,//站点id
			'member_id'       				=> $this->member_id,
			'is_balance'      				=> isset($this->params['is_balance']) ? $this->params['is_balance'] : 0,//是否使用余额
			'order_from'      				=> $this->params['app_type'],
			'order_from_name' 				=> $this->params['app_type_name'],
			'latitude'  							=> $this->params["latitude"] ?? null,
			'longitude' 							=> $this->params["longitude"] ?? null,
			'express_type'            => $this->params["express_type"] ?? 0,
			'buyer_ask_delivery_time' => $this->params["buyer_ask_delivery_time"] ?? '',
			'default_store_id'				=> $this->params["default_store_id"] ?? 0,
		];

		$res = $order_create->calculate($data);
		return $this->response($this->success($res));
	}
}