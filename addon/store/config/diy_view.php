<?php
return [
    'template' => [
    ],
    'util' => [
        [
            'name' => 'STORE_INFO',
            'title' => '门店信息',
            'type' => 'SYSTEM',
            'controller' => 'StoreInfo',
            'value' => '{}',
            'sort' => '10025',
            'support_diy_view' => 'DIY_STORE',
            'max_count' => 1,
            'icon' => 'addon/store/component/view/store_info/img/icon/store_info.png',
            'icon_selected' => 'addon/store/component/view/store_info/img/icon/store_info_selected.png'
        ],
        [
            'name' => 'STORE_CHANGE',
            'title' => '门店展示',
            'type' => 'SYSTEM',
            'controller' => 'StoreShow',
            'value' => '{style: 1, "backgroundColor": "", "textColor": "#333333", "defaultTextColor": "#333333"}',
            'sort' => '10026',
            'support_diy_view' => 'DIYVIEW_INDEX',
            'max_count' => 1,
            'icon' => 'addon/store/component/view/store_show/img/icon/store_show.png',
            'icon_selected' => 'addon/store/component/view/store_show/img/icon/store_show_selected.png'
        ]
    ],
    'link' => [
    ],
];