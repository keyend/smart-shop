<?php
namespace addon\pintuan\event;
use addon\pintuan\model\Pintuan;
/**
 * 商品营销活动信息
 */
class GoodsListPromotion
{
	/**
	 * 商品营销活动信息
	 * @param $param
	 * @return array
	 */
	public function handle($param)
	{
		if (empty($param[ 'promotion' ]) || $param[ 'promotion' ] != 'pintuan') return [];
		$condition[] = [
			['pp.site_id', '=', $param[ 'site_id' ]],
			['pp.status', '=', 1]
		];
		if ($param[ 'pintuan_name' ]) {
			$condition[] = ['pp.pintuan_name', 'like', '%' . $param[ 'pintuan_name' ] . '%'];
		}
		$model = new Pintuan();
		$list = $model->getPintuanGoodsPageList($condition, $param[ 'page' ], $param[ 'page_size' ], 'pp.pintuan_id desc');
		return $list;
	}
}