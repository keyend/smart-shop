<?php
namespace addon\pointexchange\event;
/**
 * 活动展示
 */
class ShowPromotion
{
	
	/**
	 * 活动展示
	 *
	 * @return multitype:number unknown
	 */
	public function handle()
	{
		$data = [
			
			'admin' => [
			
			],
			'shop' => [
				[
					//插件名称
					'name' => 'pointexchange',
					//展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
					'show_type' => 'tool',
					//展示主题
					'title' => '积分商城',
					//展示介绍
					'description' => '提升用户活跃度，提高用户留存率',
					//展示图标
					'icon' => 'addon/pointexchange/icon.png',
					//跳转链接
					'url' => 'pointexchange://shop/exchange/lists',
					'sort'        => '2',
				],
			
			],
		
		];
		return $data;
	}
}