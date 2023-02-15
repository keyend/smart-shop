<?php
return [
    'template' => [
    ],
    'util' => [
        [
            'name' => 'WEAPP_LIVE',
            'title' => '小程序直播',
            'type' => 'ADDON',
            'controller' => 'LiveInfo',
            'value' => '{"paddingUpDown":"0","isShowAnchorInfo":"1","isShowLiveGood":"1"}',
            'sort' => '12007',
            'support_diy_view' => '',
            'max_count' => 1,
            'icon' => 'addon/live/component/view/live_info/img/icon/live_info.png',
            'icon_selected' => 'addon/live/component/view/live_info/img/icon/live_info_selected.png'
        ]
    ],
    'link' => [
        [
            'name' => 'LIVE',
            'title' => '直播',
            'parent' => 'MARKETING_LINK',
            'wap_url' => '',
            'web_url' => '',
            'sort' => 0,
            'child_list' => [
                [
                    'name' => 'LIVE_LIST',
                    'title' => '直播',
                    'parent' => '',
                    'wap_url' => '/otherpages/live/list/list',
                    'web_url' => '',
                    'sort' => 0
                ]
            ]
        ]
    ]
];