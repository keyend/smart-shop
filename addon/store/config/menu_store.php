<?php
return [
    [
        'name' => 'ADDON_STORE_INDEX',
        'title' => '概况',
        'url' => 'store://store/index/index',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => 'addon/store/store/view/public/img/store_icon/intro.png',
        'sort' => 1,
    ],
    [
        'name' => 'ADDON_STORE_GOODS',
        'title' => '商品',
        'url' => 'store://store/goods/index',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => 'addon/store/store/view/public/img/store_icon/goods.png',
        'sort' => 2,
    ],
    [
        'name' => 'STORE_STOCK_IMPORT',
        'title' => '入库',
        'url' => 'store://store/storestockimport/lists',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => 'addon/store/store/view/public/img/store_icon/stock_import.png',
        'sort' => 3,
        'child_list' => [
            [
                'name' => 'STORE_STOCK_IMPORT_DETAIL',
                'title' => '详情',
                'url' => 'store://store/storestockimport/detail',
                'is_show' => 0,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 2,
                'child_list' => []
            ],
        ]
    ],
    [
        'name' => 'ADDON_STORE_ORDER',
        'title' => '订单',
        'url' => 'store://store/order/lists',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => 'addon/store/store/view/public/img/store_icon/order.png',
        'sort' => 4,
        'child_list' => [
            [
                'name' => 'ADDON_STORE_ORDER_MANAGE',
                'title' => '订单列表',
                'url' => 'store://store/order/lists',
                'parent' => '',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 1,
                'child_list' => [
                    [
                        'name' => 'ADDON_STORE_ORDER_DETAIL',
                        'title' => '自提订单详情',
                        'url' => 'store://store/order/detail',
                        'is_show' => 0,
                        'is_control' => 1,
                        'is_icon' => 0,
                        'picture' => '',
                        'picture_selected' => '',
                        'sort' => 1,
                    ],
                ],
            ],
            [
                'name' => 'ADDON_STORE_ORDER_VERIFY',
                'title' => '核销台',
                'url' => 'store://store/verify/verifycard',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 2,
                'child_list' => []
            ],
        ]
    ],
    [
        'name' => 'ADDON_STORE_CASH',
        'title' => '收银台',
        'url' => 'store://store/cash/cash',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => 'addon/store/store/view/public/img/store_icon/sellte.png',
        'sort' => 5,
        'child_list' => []
    ],
    [
        'name' => 'ADDON_STORE_ACCOUNT',
        'title' => '结算',
        'url' => 'store://store/settlement/index',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => 'addon/store/store/view/public/img/store_icon/settlement.png',
        'sort' => 6,
        'child_list' => []
    ],
    [
        'name' => 'ADDON_STORE_MEMBER',
        'title' => '会员',
        'url' => 'store://store/member/index',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => 'addon/store/store/view/public/img/store_icon/member.png',
        'sort' => 7,
        'child_list' => [
            [
                'name' => 'ADDON_STORE_MEMBER_INDEX',
                'title' => '会员概况',
                'url' => 'store://store/member/index',
                'sort' => 1,
                'is_show' => 1
            ],
            [
                'name' => 'ADDON_STORE_MEMBER_LIST',
                'title' => '会员列表',
                'url' => 'store://store/member/lists',
                'sort' => 2,
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'ADDON_STORE_MEMBER_DETAIL',
                        'title' => '会员详情',
                        'url' => 'store://store/member/detail',
                        'sort' => 1,
                        'is_show' => 1
                    ]
                ]
            ],
        ]
    ],
    [
        'name' => 'ADDON_STORE_USER',
        'title' => '用户',
        'url' => 'store://store/user/index',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => 'addon/store/store/view/public/img/store_icon/user.png',
        'sort' => 8,
        'child_list' => [
            [
                'name' => 'ADDON_STORE_USER_LIST',
                'title' => '用户管理',
                'url' => 'store://store/user/index',
                'sort' => 1,
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'ADDON_STORE_USER_ADD',
                        'title' => '添加用户',
                        'url' => 'store://store/user/addUser',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'ADDON_STORE_USER_EDIT',
                        'title' => '修改用户',
                        'url' => 'store://store/user/editUser',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                ]
            ],
            [
                'name' => 'ADDON_STORE_USERLOG',
                'title' => '用户日志',
                'url' => 'store://store/user/userlog',
                'sort' => 2,
                'is_show' => 1
            ],
            [
                'name' => 'ADDON_STORE_USER_GROUP',
                'title' => '用户组',
                'url' => 'store://store/user/group',
                'sort' => 3,
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'ADDON_STORE_USER_GROUP_ADD',
                        'title' => '添加用户',
                        'url' => 'store://store/user/addGroup',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'ADDON_STORE_USER_GROUP_EDIT',
                        'title' => '修改用户',
                        'url' => 'store://store/user/editGroup',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                ]
            ],
        ]
    ],
    [
        'name' => 'ADDON_STORE_INFO',
        'title' => '门店',
        'url' => 'store://store/store/config',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => 'addon/store/store/view/public/img/store_icon/info.png',
        'sort' => 9,
        'child_list' => []
    ],
    [
        'name' => 'ADDON_STORE_DIY',
        'title' => '装修',
        'url' => 'store://store/diy/index',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => 'addon/store/store/view/public/img/store_icon/diy.png',
        'sort' => 10,
        'child_list' => []
    ]
];