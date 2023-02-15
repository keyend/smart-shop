<?php
return [
    [
        'name' => 'PROMOTION_MEMBERRECHARGE',
        'title' => '会员充值',
        'url' => 'memberrecharge://shop/memberrecharge/lists',
        'parent' => 'PROMOTION_ROOT',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'sort' => 8,
        'child_list' => [
            [
                'name' => 'PROMOTION_MEMBERRECHARGE_LIST',
                'title' => '充值套餐',
                'url' => 'memberrecharge://shop/memberrecharge/lists',
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_MEMBERRECHARGE_ADD',
                        'title' => '添加充值套餐',
                        'url' => 'memberrecharge://shop/memberrecharge/add',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_MEMBERRECHARGE_EDIT',
                        'title' => '编辑充值套餐',
                        'url' => 'memberrecharge://shop/memberrecharge/edit',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_MEMBERRECHARGE_DETAIL',
                        'title' => '充值套餐详情',
                        'url' => 'memberrecharge://shop/memberrecharge/detail',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_MEMBERRECHARGE_INVALID',
                        'title' => '停用充值套餐',
                        'url' => 'memberrecharge://shop/memberrecharge/invalid',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_MEMBERRECHARGE_DELETE',
                        'title' => '删除充值套餐',
                        'url' => 'memberrecharge://shop/memberrecharge/delete',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_MEMBERRECHARGE_CARD_LISTS',
                        'title' => '开卡列表',
                        'url' => 'memberrecharge://shop/memberrecharge/card_lists',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_MEMBERRECHARGE_CARD_DETAIL',
                        'title' => '开卡详情',
                        'url' => 'memberrecharge://shop/memberrecharge/card_detail',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_MEMBERRECHARGE_SET_CONFIG',
                        'title' => '充值开关',
                        'url' => 'memberrecharge://shop/memberrecharge/setConfig',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                ]
            ],
            [
                'name' => 'PROMOTION_MEMBERRECHARGE_ORDER_LISTS',
                'title' => '订单列表',
                'url' => 'memberrecharge://shop/memberrecharge/order_lists',
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_MEMBERRECHARGE_ORDER_DETAIL',
                        'title' => '订单详情',
                        'url' => 'memberrecharge://shop/memberrecharge/order_detail',
                        'sort' => 1,
                        'is_show' => 0
                    ]
                ]
            ],
        ]
    ],
];