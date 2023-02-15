<?php
return [
    [
        'name' => 'ADDON_STORE_SHOP_STORE_CONFIG',
        'title' => '门店设置',
        'url' => 'store://shop/config/index',
        'parent' => 'SYSTEM_CONFIG',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
    ],
    [
        'name' => 'ADDON_STORE_SHOP_STORE_SETTLEMENT',
        'title' => '门店结算',
        'url' => 'store://shop/settlement/index',
        'parent' => 'ACCOUNT_ROOT',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'sort' => 5,
        'picture' => 'addon/store/shop/view/public/img/shop_icon/settlement.png',
        'child_list' => [
            [
                'name' => 'ADDON_STORE_SHOP_STORE_SETTLEMENT_INFO',
                'title' => '结算详情',
                'url' => 'store://shop/settlement/detail',
                'is_show' => 0,
                'is_control' => 1,
                'sort' => 1,
            ],
            [
                'name' => 'ADDON_STORE_SHOP_STORE_SETTLEMENT_SETTLEMENT',
                'title' => '结算',
                'url' => 'store://shop/settlement/settlement',
                'is_show' => 0,
                'is_control' => 1,
                'sort' => 2,
            ]
        ]
    ]
];