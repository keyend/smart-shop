<?php
namespace addon\pointexchange\model;
use app\model\BaseModel;
use addon\coupon\model\Coupon;
use app\model\goods\Goods;
/**
 * 积分兑换
 */
class Exchange extends BaseModel
{
	public $type = [
		1 => [
			'name' => 'goods',
			'title' => '兑换商品',
		],
		2 => [
			'name' => 'coupon',
			'title' => '兑换优惠券',
		],
		3 => [
			'name' => 'balance',
			'title' => '兑换红包',
		],
	];
	/**
	 * 添加积分兑换
	 * @param array $data
	 */
	public function addExchange($data)
	{
		$save_data = [
			'site_id' => $data[ 'site_id' ],
			'type' => $data[ 'type' ],
			'point' => $data[ 'point' ],
			'state' => $data[ 'state' ],
			'type_name' => $data[ 'type_name' ],
			'create_time' => time()
		];
		if ($data[ 'type' ] == 1) {
			$exist = model('promotion_exchange')->getInfo([ [ 'type_id', '=', $data[ 'sku_id' ] ] ], 'id');
			if (!empty($exist)) {
				return $this->error('', '该商品已存在，请不要重复添加');
			}
			$sku_model = new Goods();
			$sku_info = $sku_model->getGoodsSkuInfo([ [ 'sku_id', '=', $data[ 'sku_id' ] ] ], 'sku_name,sku_image,price,stock,goods_content');
			$sku_info = $sku_info[ 'data' ];
			$save_data[ 'type_id' ] = $data[ 'sku_id' ];
			$save_data[ 'name' ] = $sku_info[ 'sku_name' ];
			$save_data[ 'image' ] = $sku_info[ 'sku_image' ];
			$save_data[ 'stock' ] = $sku_info[ 'stock' ];
			$save_data[ 'pay_type' ] = $data[ 'pay_type' ];
			$save_data[ 'market_price' ] = $sku_info[ 'price' ];
			$save_data[ 'price' ] = $data[ 'price' ];
			$save_data[ 'limit_num' ] = $data[ 'limit_num' ];
			$save_data[ 'content' ] = $sku_info[ 'goods_content' ];
		} elseif ($data[ 'type' ] == 2) {
			$exist = model('promotion_exchange')->getInfo([ [ 'type_id', '=', $data[ 'coupon_type_id' ] ] ], 'id');
			if (!empty($exist)) {
				return $this->error('', '该优惠券已存在，请不要重复添加');
			}
			$coupon = new Coupon();
			$coupon_type_info = $coupon->getCouponTypeInfo([ [ 'coupon_type_id', '=', $data[ 'coupon_type_id' ] ] ], 'coupon_type_id,coupon_name,money,count,image,status,type,discount');
			$coupon_type_info = $coupon_type_info[ 'data' ];
			$save_data [ 'type_id' ] = $data[ 'coupon_type_id' ];
			$save_data [ 'name' ] = $coupon_type_info[ 'coupon_name' ];
			$save_data [ 'image' ] = $coupon_type_info[ 'image' ];
			$save_data [ 'stock' ] = $coupon_type_info[ 'count' ];
			$save_data [ 'content' ] = $data[ 'content' ];
			if ($coupon_type_info[ 'type' ] == 'reward') {
				$save_data[ 'market_price' ] = $coupon_type_info[ 'money' ];
			} elseif ($coupon_type_info[ 'type' ] == 'discount') {
				$save_data[ 'market_price' ] = $coupon_type_info[ 'discount' ];
			}
		} elseif ($data[ 'type' ] == 3) {
			$save_data [ 'name' ] = $data[ 'name' ];
			$save_data [ 'image' ] = $data[ 'image' ];
			$save_data [ 'stock' ] = $data[ 'stock' ];
			$save_data [ 'balance' ] = $data[ 'balance' ];
			$save_data [ 'market_price' ] = $data[ 'balance' ];
			$save_data [ 'content' ] = $data[ 'content' ];
		}
		$res = model("promotion_exchange")->add($save_data);
		return $this->success($res);
	}
	/**
	 * 编辑积分兑换
	 * @param array $data
	 */
	public function editExchange($data)
	{
		$save_data = [
			'site_id' => $data[ 'site_id' ],
			'type' => $data[ 'type' ],
			'point' => $data[ 'point' ],
			'state' => $data[ 'state' ],
			'modify_time' => time()
		];
		if ($data[ 'type' ] == 1) {
			$sku_model = new Goods();
			$sku_info = $sku_model->getGoodsSkuInfo([ [ 'sku_id', '=', $data[ 'sku_id' ] ] ], 'stock,goods_content');
			$sku_info = $sku_info[ 'data' ];
			$save_data [ 'pay_type' ] = $data[ 'pay_type' ];
			$save_data [ 'price' ] = $data[ 'price' ];
			$save_data [ 'limit_num' ] = $data[ 'limit_num' ];
			$save_data [ 'stock' ] = $sku_info[ 'stock' ];
			$save_data[ 'content' ] = $sku_info[ 'goods_content' ];
		} elseif ($data[ 'type' ] == 2) {
			$coupon = new Coupon();
			$coupon_type_info = $coupon->getCouponTypeInfo([ [ 'coupon_type_id', '=', $data[ 'coupon_type_id' ] ] ], 'coupon_type_id,coupon_name,money,count,image,status,type,discount');
			$coupon_type_info = $coupon_type_info[ 'data' ];
			$save_data [ 'type_id' ] = $data[ 'coupon_type_id' ];
			$save_data [ 'name' ] = $coupon_type_info[ 'coupon_name' ];
			$save_data [ 'image' ] = $coupon_type_info[ 'image' ];
			$save_data [ 'stock' ] = $coupon_type_info[ 'count' ];
			$save_data [ 'content' ] = $data[ 'content' ];
			if ($coupon_type_info[ 'type' ] == 'reward') {
				$save_data[ 'market_price' ] = $coupon_type_info[ 'money' ];
			} elseif ($coupon_type_info[ 'type' ] == 'discount') {
				$save_data[ 'market_price' ] = $coupon_type_info[ 'discount' ];
			}
		} elseif ($data[ 'type' ] == 3) {
			$save_data [ 'name' ] = $data[ 'name' ];
			$save_data [ 'image' ] = $data[ 'image' ];
			$save_data [ 'stock' ] = $data[ 'stock' ];
			$save_data [ 'balance' ] = $data[ 'balance' ];
			$save_data [ 'market_price' ] = $data[ 'balance' ];
			$save_data [ 'content' ] = $data[ 'content' ];
		}
		$res = model("promotion_exchange")->update($save_data, [ [ 'id', '=', $data[ 'id' ] ], [ 'site_id', '=', $data[ 'site_id' ] ] ]);
		return $this->success($res);
	}
	/**
	 * 删除积分兑换
	 * @param string $ids
	 */
	public function deleteExchange($ids)
	{
		$res = model("promotion_exchange")->delete([ [ 'id', 'in', $ids ] ]);
		return $this->success($res);
	}
	/**
	 * 获取积分兑换信息
	 * @param int $id
	 */
	public function getExchangeInfo($id, $field = '*')
	{
		$info = model("promotion_exchange")->getInfo([ [ 'id', '=', $id ] ], $field);
		if (!empty($info) && !empty($info[ 'type' ])) {
			switch ( $info[ 'type' ] ) {
				case 1:
					//商品
					break;
				case 2:
					//优惠券
					$coupon = new Coupon();
					$coupon_type_info = $coupon->getCouponTypeInfo([ [ 'coupon_type_id', '=', $info[ 'type_id' ] ], [ 'is_show', '=', 1 ], [ 'is_forbidden', '=', 0 ] ], 'type as coupon_type,discount_limit');
					$coupon_type_info = $coupon_type_info[ 'data' ];
					if (!empty($coupon_type_info)) {
						$info = array_merge($info, $coupon_type_info);
					} else {
						$info = [];
					}
					break;
				case 3:
					//余额红包
					break;
			}
		}
		return $this->success($info);
	}
	/**
	 * 获取积分兑换商品详情
	 * @param $id
	 * @param $site_id
	 * @return array
	 */
	public function getExchangeGoodsDetail($id, $site_id)
	{
		$info = model("promotion_exchange")->getInfo([ [ 'id', '=', $id ], [ 'site_id', '=', $site_id ] ], 'id,type,type_name,type_id,name,image,stock,pay_type,point,market_price,price,limit_num,delivery_price,balance,state,content');
		// 兑换类型，1：商品，2：优惠券，3：红包
		switch ( $info[ 'type' ] ) {
			case 1:
				//商品
				$goods = new Goods();
				$goods_sku_info = $goods->getGoodsSkuInfo([ [ 'sku_id', '=', $info[ 'type_id' ] ], [ 'site_id', '=', $site_id ], [ 'goods_state', '=', 1 ], [ 'is_delete', '=', 0 ] ], 'sku_id,sku_name,discount_price,stock as goods_stock,sku_image,sku_images,goods_id,site_id,goods_content');
				$goods_sku_info = $goods_sku_info[ 'data' ];
				if (!empty($goods_sku_info)) {
					$info = array_merge($info, $goods_sku_info);
				} else {
					$info = [];
				}
				break;
			case 2:
				//优惠券
				$coupon = new Coupon();
				$coupon_type_info = $coupon->getCouponTypeInfo([ [ 'coupon_type_id', '=', $info[ 'type_id' ] ], [ 'is_show', '=', 1 ], [ 'is_forbidden', '=', 0 ] ], 'coupon_type_id,coupon_name,money,count,status,lead_count,max_fetch,at_least,end_time,validity_type,fixed_term,goods_type,is_limit,type as coupon_type,discount_limit,discount');
				$coupon_type_info = $coupon_type_info[ 'data' ];
				if (!empty($coupon_type_info)) {
					$info = array_merge($info, $coupon_type_info);
				} else {
					$info = [];
				}
				break;
			case 3:
				//余额红包
				break;
		}
		if (!empty($info)) {
			return $this->success($info);
		} else {
			return $this->error();
		}
	}
	/**
	 * 获取积分兑换列表
	 * @param unknown $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 * @return multitype:string
	 */
	public function getExchangeList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$list = model('promotion_exchange')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	/**
	 * 获取积分兑换列表
	 * @param unknown $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @param string $alias
	 * @param array $join
	 */
	public function getExchangePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = '*', $alias = '', $join = [])
	{
		$list = model('promotion_exchange')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	/**
	 * 增加库存
	 * @param $param
	 */
	public function incStock($param)
	{
		$condition = array (
			[ "id", "=", $param[ "id" ] ]
		);
		$num = $param[ "num" ];
		$info = model("promotion_exchange")->getInfo($condition, "stock, name");
		if (empty($info))
			return $this->error(-1, "");
		//编辑sku库存
		$result = model("promotion_exchange")->setInc($condition, "stock", $num);
		return $this->success($result);
	}
	/**
	 * 减少库存
	 * @param $param
	 */
	public function decStock($param)
	{
		$condition = array (
			[ "id", "=", $param[ "id" ] ]
		);
		$num = $param[ "num" ];
		$info = model("promotion_exchange")->getInfo($condition, "stock, name");
		if (empty($info))
			return $this->error();
		if ($info[ "stock" ] < 0)
			return $this->error('', $info[ "name" ] . "库存不足!");
		//编辑sku库存
		$result = model("promotion_exchange")->setDec($condition, "stock", $num);
		if ($result === false)
			return $this->error();
		return $this->success($result);
	}
}