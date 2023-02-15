<?php
return [
	'template' => [
	],
	'util' => [
	],
	'link' => [
        [
            'name' => 'INTEGRAL',
            'title' => '积分商城',
            'parent' => 'MARKETING_LINK',
            'wap_url' => '',
            'web_url' => '',
            'sort' => 0,
            'child_list' => [
                [
                    'name' => 'INTEGRAL_STORE',
                    'title' => '积分商城',
                    'parent' => '',
                    'wap_url' => '/promotionpages/point/list/list',
                    'web_url' => '',
                    'sort' => 0
                ],
                [
                    'name' => 'STORE_MY_INTEGRAL',
                    'title' => '我的积分',
                    'parent' => '',
                    'wap_url' => '/otherpages/member/point/point',
                    'web_url' => '',
                    'sort' => 0
                ],
                [
                    'name' => 'INTEGRAL_CONVERSION',
                    'title' => '积分兑换',
                    'parent' => '',
                    'wap_url' => '/promotionpages/point/order_list/order_list',
                    'web_url' => '',
                    'sort' => 0
                ]
            ]
        ]
	],
];