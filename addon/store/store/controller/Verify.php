<?php
namespace addon\store\store\controller;
use app\model\verify\Verify as VerifyModel;
/**
 * 核销
 * Class Verify
 * @package app\shop\controller
 */
class Verify extends BaseStore
{
	
	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
	}
	
	/**
	 * 核销记录
	 * @return mixed
	 */
	public function records()
	{
		$verify_model = new VerifyModel();
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$order = input("order", "create_time desc");
			$verify_type = input('verify_type', "");//验证类型
			$verify_code = input('verify_code', "");//验证码
			$verifier_name = input('verifier_name', "");
			$start_time = input("start_time", '');
			$end_time = input("end_time", '');
			
			$condition = [
				[ 'site_id', "=", $this->site_id ],
				[ 'is_verify', '=', 1 ]
			];
			if (!empty($verify_type)) {
				$condition[] = [ "verify_type", "=", $verify_type ];
			}
			if (!empty($verify_code)) {
				$condition[] = [ "verify_code", 'like', '%' . $verify_code . '%' ];
			}
			if (!empty($verifier_name)) {
				$condition[] = [ 'verifier_name', 'like', '%' . $verifier_name . '%' ];
			}
			if (!empty($start_time) && empty($end_time)) {
				$condition[] = [ 'create_time', '>=', date_to_time($start_time) ];
			} elseif (empty($start_time) && !empty($end_time)) {
				$condition[] = [ "create_time", "<=", date_to_time($end_time) ];
			} elseif (!empty($start_time) && !empty($end_time)) {
				$condition[] = [ 'create_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
			}
			$list = $verify_model->getVerifyPageList($condition, $page, $page_size, $order, $field = 'id, verify_code, verify_type, verify_type_name, verify_content_json, verifier_id, verifier_name, is_verify, create_time, verify_time');
			return $list;
		} else {
			$verify_type = $verify_model->getVerifyType();
			$this->assign('verify_type', $verify_type);
			return $this->fetch("verify/records", [], $this->replace);
		}
		
	}
	
	/**
	 * 核销台
	 * @return mixed
	 */
	public function verifyCard()
	{
		if (request()->isAjax()) {
			$verify_code = input("verify_code", "");
			$verify_model = new VerifyModel();
			$res = $verify_model->getVerifyInfo([ [ "verify_code", "=", $verify_code ], [ "site_id", "=", $this->site_id ] ]);
			$json_array = $res['data'];
			if (isset($json_array['data']['item_array'])) {
				try {
					foreach($json_array['data']['item_array'] as $row) {
						$goods_info = model('order_goods')->getInfo([['order_goods_id', '=', $row['order_goods_id']]], "order_id");
						$order_info = model('order')->getInfo([["order_id", '=', $goods_info['order_id']]], "order_id,delivery_store_id");
						if ($order_info['delivery_store_id'] != $this->store_id) {
							return app()->make(\app\model\BaseModel::class)->error([], "找不到核销码信息!");
						}
					}
				} catch(\Exception $e) {
					return app()->make(\app\model\BaseModel::class)->error([], "找不到核销码信息!");
				}
			}
			return $res;
		} else {
			return $this->fetch("verify/verify_card", [], $this->replace);
		}
		
	}
	
	/**
	 * 核销
	 */
	public function verify()
	{
		$verify_code = input("verify_code", "");
		$verify_model = new VerifyModel();
		$info = array(
			"verifier_id" => $this->uid,
			"verifier_name" => $this->user_info['username'],
		);
		$res = $verify_model->verify($info, $verify_code);
		return $res;
	}
	
}