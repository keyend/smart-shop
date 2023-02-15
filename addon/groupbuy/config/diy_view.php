<?php
return [
    'template' => [
    ],
    'util' => [
        [
            'name' => 'GROUPBUY_LIST',
            'title' => '团购',
            'type' => 'ADDON',
            'controller' => 'Groupbuy',
            'value' => '{"sources" : "default", "categoryId" : 0, "goodsCount" : "6", "goodsId": [], "style": 1, "backgroundColor": "", "padding": 10, "list": {"imageUrl": "","title": "团购专区"}, "listMore": {"imageUrl": "","title": "查看更多"}, "titleTextColor": "#000", "defaultTitleTextColor": "#000", "moreTextColor": "#858585", "defaultMoreTextColor": "#858585"}',
            'sort' => '12001',
            'support_diy_view' => '',
            'max_count' => 0,
            'icon' => 'addon/groupbuy/component/view/groupbuy/img/icon/groupbuy.png',
            'icon_selected' => 'addon/groupbuy/component/view/groupbuy/img/icon/groupbuy_selected.png'
        ]
    ],
    'link' => [
        [
            'name' => 'GROUPBUY',
            'title' => '团购',
            'parent' => 'MARKETING_LINK',
            'wap_url' => '',
            'web_url' => '',
            'sort' => 0,
            'child_list' => [
                [
                    'name' => 'GROUPBUY_PREFECTURE',
                    'title' => '团购专区',
                    'parent' => '',
                    'wap_url' => '/promotionpages/groupbuy/list/list',
                    'web_url' => '',
                    'sort' => 0
                ]
            ]
        ],
        [
            'name' => 'GROUPBUY_GOODS',
            'title' => '团购商品',
            'parent' => 'COMMODITY',
            'wap_url' => '',
            'web_url' => '',
            'child_list' => []
        ]
    ],
];