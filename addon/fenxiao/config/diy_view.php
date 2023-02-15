<?php
return [
    'template' => [
    ],
    'util' => [
        [
            'name' => 'FENXIAO_GOODS_LIST',
            'title' => '分销商品',
            'type' => 'ADDON',
            'controller' => 'FenxiaoGoodsList',
            'value' => '{"sources" : "default", "categoryId" : 0, "goodsCount" : "6", "goodsId": [], "style": 1, "backgroundColor": "", "padding": 10, "list": {"imageUrl": "","title": "分销商品"}, "listMore": {"imageUrl": "","title": "查看更多"}, "titleTextColor": "#000", "defaultTitleTextColor": "#000", "moreTextColor": "#858585", "defaultMoreTextColor": "#858585"}',
            'sort' => '12008',
            'support_diy_view' => 'DIY_FENXIAO_MARKET',
            'max_count' => 0,
            'icon' => 'addon/fenxiao/component/view/goods_list/img/icon/fx_goods_list.png',
            'icon_selected' => 'addon/fenxiao/component/view/goods_list/img/icon/fx_goods_list_selected.png'
        ]
    ],
    'link' => [
        [
            'name' => 'DISTRIBUTION',
            'title' => '分销',
            'parent' => 'MALL_LINK',
            'wap_url' => '/pages/index/index/index',
            'web_url' => '',
            'sort' => 2,
            'child_list' => [
                [
                    'name' => 'DISTRIBUTION_CENTRE',
                    'title' => '分销中心',
                    'parent' => '',
                    'wap_url' => '/otherpages/fenxiao/index/index',
                    'web_url' => '',
                    'sort' => 0
                ],
                [
                    'name' => 'WITHDRAWAL_SUBSIDIARY',
                    'title' => '提现明细',
                    'parent' => '',
                    'wap_url' => '/otherpages/fenxiao/withdraw_list/withdraw_list',
                    'web_url' => '',
                    'sort' => 0
                ],
                [
                    'name' => 'DISTRIBUTION_ORDER',
                    'title' => '分销订单',
                    'parent' => '',
                    'wap_url' => '/otherpages/fenxiao/order/order',
                    'web_url' => '',
                    'sort' => 0
                ],
                [
                    'name' => 'DISTRIBUTION_MARKET',
                    'title' => '分销市场',
                    'parent' => '',
                    'wap_url' => '/otherpages/diy/diy/diy?name=DIY_FENXIAO_MARKET',
                    'web_url' => '',
                    'sort' => 0
                ],
                [
                    'name' => 'DISTRIBUTION_GOODS',
                    'title' => '分销商品',
                    'parent' => '',
                    'wap_url' => '/otherpages/fenxiao/follow/follow',
                    'web_url' => '',
                    'sort' => 0
                ],
                [
                    'name' => 'DISTRIBUTION_TEAM',
                    'title' => '分销团队',
                    'parent' => '',
                    'wap_url' => '/otherpages/fenxiao/team/team',
                    'web_url' => '',
                    'sort' => 0
                ],
                [
                    'name' => 'PROMOTION_POSTER',
                    'title' => '推广海报',
                    'parent' => '',
                    'wap_url' => '/otherpages/fenxiao/promote_code/promote_code',
                    'web_url' => '',
                    'sort' => 0
                ],
            ]
        ],
        [
            'name' => 'DISTRIBUTION_GOODS',
            'title' => '分销商品',
            'parent' => 'COMMODITY',
            'wap_url' => '',
            'web_url' => '',
            'child_list' => []
        ],
    ],
];