<?php
namespace addon\pointexchange\event;
/**
 * 订单营销活动类型
 */
class OrderPromotionType
{
	
	/**
	 * 订单营销活动类型
	 * @return multitype:number unknown
	 */
	public function handle()
	{
        return ["name" => "积分兑换", "type" => "pointexchange"];
	}
}