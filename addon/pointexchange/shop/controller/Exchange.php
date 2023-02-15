<?php
namespace addon\pointexchange\shop\controller;
use addon\pointexchange\model\Exchange as ExchangeModel;
use app\shop\controller\BaseShop;
/**
 * 积分兑换
 * @author Administrator
 *
 */
class Exchange extends BaseShop
{
	/**
	 * 积分兑换列表
	 */
	public function lists()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');
			$type = input('type', '');
			$state = input('state', '');
			$condition[] = [ 'site_id', '=', $this->site_id ];
			if ($search_text) {
				$condition[] = [ 'name', 'like', '%' . $search_text . '%' ];
			}
			if ($type) {
				$condition[] = [ 'type', '=', $type ];
			}
			if ($state != '') {
				$condition[] = [ 'state', '=', $state ];
			}
			$order = 'create_time desc';
			$field = '*';
			
			$exchange_model = new ExchangeModel();
			//兑换名称 兑换图片 兑换库存  兑换价格
			return $exchange_model->getExchangePageList($condition, $page, $page_size, $order, $field);
		}
		$this->forthMenu();
		return $this->fetch("exchange/lists");
	}
	
	/**
	 * 添加积分兑换
	 */
	public function add()
	{
		if (request()->isAjax()) {
			$type = input('type', '1');//兑换类型 1 商品 2 优惠券 3 红包
			
			$data = [
				'site_id' => $this->site_id,
				'type' => $type,//兑换类型 1 商品 2 优惠券 3 红包
				'point' => input('point', ''),//积分
				'state' => input('state', ''),
			];
			if ($type == 1) {
				$data['sku_id'] = input('sku_id', '0');//商品id
				$data['pay_type'] = input('pay_type', '0');//兑换方式
				$data['price'] = input('price', '0');//金额
				$data['limit_num'] = input('limit_num', '0');//限制兑换数量
				$data['type_name'] = '商品';
			} elseif ($type == 2) {
				$data ['coupon_type_id'] = input('coupon_type_id', '0');//优惠券id
				$data ['content'] = input('content', '');
				$data ['type_name'] = '优惠券';
			} elseif ($type == 3) {
				$data ['name'] = input('name', '');
				$data ['image'] = input('image', '');
				$data ['stock'] = input('stock', '');
				$data ['balance'] = input('balance', '0');
				$data ['content'] = input('content', '');
				$data ['type_name'] = '红包';
			} else {
				return error(-1, '');
			}
			$exchange_model = new ExchangeModel();
			$res = $exchange_model->addExchange($data);
			return $res;
		} else {
			return $this->fetch("exchange/add");
		}
	}
	
	/**
	 * 编辑积分兑换
	 */
	public function edit()
	{
		$id = input("id", 0);
		$exchange_model = new ExchangeModel();
		if (request()->isAjax()) {
			$type = input('type', '1');//兑换类型 1 商品 2 优惠券 3 红包
			$data = [
				'site_id' => $this->site_id,
				'type' => $type,//兑换类型 1 商品 2 优惠券 3 红包
				'point' => input('point', ''),//积分
				'state' => input('state', ''),
				'id' => $id
			];
			if ($type == 1) {
				$data['pay_type'] = input('pay_type', '0');//兑换方式
				$data['sku_id'] = input('sku_id', '0');//商品id
				$data['price'] = input('price', '0');//金额
				$data['limit_num'] = input('limit_num', '0');//限制兑换数量
			} elseif ($type == 2) {
				$data ['coupon_type_id'] = input('coupon_type_id', '0');//优惠券id
				$data ['content'] = input('content', '');
			} elseif ($type == 3) {
				$data['name'] = input('name', '');
				$data['image'] = input('image', '');
				$data['stock'] = input('stock', '');
				$data['balance'] = input('balance', '0');
				$data['content'] = input('content', '');
			} else {
				return error(-1, '');
			}
			
			$res = $exchange_model->editExchange($data);
			return $res;
		} else {
			$exchange_info = $exchange_model->getExchangeInfo($id);
			if (empty($exchange_info['data'])) {
				$this->error();
			}
			$this->assign("exchange_info", $exchange_info['data']);
			return $this->fetch("exchange/edit");
		}
	}
	
	/**
	 *关闭积分兑换
	 */
	public function delete()
	{
		$id = input("id", 0);
		$exchange_model = new ExchangeModel();
		$res = $exchange_model->deleteExchange($id);
		return $res;
		
	}
}