<?php
// 事件定义文件
return [
	'bind' => [
	
	],
	
	'listen' => [
		//展示活动
		'ShowPromotion' => [
			'addon\pointexchange\event\ShowPromotion',
		],
		'PointexchangeOrderPayNotify' => [
			'addon\pointexchange\event\PointexchangeOrderPayNotify',
		],
		
		'MemberAccountFromType' => [
			'addon\pointexchange\event\MemberAccountFromType',
		],
		'OrderPay' => [
			'addon\pointexchange\event\OrderPay'
		],
        'OrderClose' => [
            'addon\pointexchange\event\OrderClose'
        ],
        // 订单营销活动类型
        'OrderPromotionType' => [
            'addon\pointexchange\event\OrderPromotionType',
        ],
	],
	
	'subscribe' => [
	],
];