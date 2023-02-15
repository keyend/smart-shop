<?php
return [
    'template' => [
    ],
    'util' => [
        [
            'name' => 'SECKILL_LIST',
            'title' => '秒杀',
            'type' => 'ADDON',
            'controller' => 'Seckill',
            'value' => '{style: 1, "backgroundColor": "", "padding": 10, "paddingLeftRight": 0, "isShowGoodsName": 1, "isShowGoodsDesc": 0, "isShowGoodsPrice": 1, "isShowGoodsPrimary": 1, "isShowGoodsStock": 0, "list": {"imageUrl": "","title": "秒杀专区"}, "listMore": {"imageUrl": "","title": "更多秒杀"}, "titleTextColor": "#000", "defaultTitleTextColor": "#000", "moreTextColor": "#858585", "defaultMoreTextColor": "#858585"}',
            'sort' => '12004',
            'support_diy_view' => '',
            'max_count' => 1,
            'icon' => 'addon/seckill/component/view/seckill/img/icon/seckill.png',
            'icon_selected' => 'addon/seckill/component/view/seckill/img/icon/seckill_selected.png'
        ]
    ],
    'link' => [
        [
            'name' => 'SECKILL',
            'title' => '秒杀',
            'parent' => 'MARKETING_LINK',
            'wap_url' => '',
            'web_url' => '',
            'sort' => 0,
            'child_list' => [
                [
                    'name' => 'SECKILL_PREFECTURE',
                    'title' => '秒杀专区',
                    'parent' => '',
                    'wap_url' => '/promotionpages/seckill/list/list',
                    'web_url' => '',
                    'sort' => 0
                ]
            ]
        ]
    ],
];