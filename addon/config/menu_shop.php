<?php
return [
    [
        'name' => 'TTAPP_ROOT',
        'title' => '字节小程序',
        'url' => 'ttapp://shop/ttapp/setting',
        'parent' => 'CHANNEL_ROOT',
        'picture_select' => '',
        'picture' => 'addon/ttapp/shop/view/public/img/menu_icon/tt_app.png',
        'is_show' => 1,
        'sort' => 4,
        'child_list' => [
            [
                'name' => 'TTAPP_CONFIG',
                'title' => '小程序配置',
                'url' => 'ttapp://shop/ttapp/config',
                'is_show' => 0
            ],
            [
                'name' => 'TTAPP_PACKAGE',
                'title' => '小程序包管理',
                'url' => 'ttapp://shop/ttapp/package',
                'is_show' => 0
            ],
    ],
    ]
];