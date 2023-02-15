<?php
return [
    'template' => [
    ],
    'util' => [
        [
            'name' => 'PINTUAN_LIST',
            'title' => '拼团',
            'type' => 'ADDON',
            'controller' => 'Pintuan',
            'value' => '{"sources" : "default", "categoryId" : 0, "goodsCount" : "6", "goodsId": [], "style": 1, "backgroundColor": "", "padding": 10, "list": {"imageUrl": "","title": "拼团专区"}, "listMore": {"imageUrl": "","title": "好友都在拼"}, "titleTextColor": "#000", "defaultTitleTextColor": "#000", "moreTextColor": "#858585", "defaultMoreTextColor": "#858585"}',
            'sort' => '12003',
            'support_diy_view' => '',
            'max_count' => 0,
            'icon' => 'addon/pintuan/component/view/pintuan/img/icon/pintuan.png',
            'icon_selected' => 'addon/pintuan/component/view/pintuan/img/icon/pintuan_selected.png'
        ]
    ],
    'link' => [
        [
            'name' => 'PINTUAN',
            'title' => '拼团',
            'parent' => 'MARKETING_LINK',
            'wap_url' => '',
            'web_url' => '',
            'sort' => 0,
            'child_list' => [
                [
                    'name' => 'PINTUAN_PREFECTURE',
                    'title' => '拼团专区',
                    'parent' => '',
                    'wap_url' => '/promotionpages/pintuan/list/list',
                    'web_url' => '',
                    'sort' => 0
                ],
                [
                    'name' => 'MY_PINTUAN',
                    'title' => '我的拼团',
                    'parent' => '',
                    'wap_url' => '/promotionpages/pintuan/my_spell/my_spell',
                    'web_url' => '',
                    'sort' => 0
                ],
            ]
        ],
        [
            'name' => 'PINTUAN_GOODS',
            'title' => '拼团商品',
            'parent' => 'COMMODITY',
            'wap_url' => '',
            'web_url' => '',
            'child_list' => []
        ],
    ],
];