<?php
return [
    [
        'name' => 'LIVE_ROOT',
        'title' => '小程序直播',
        'url' => 'live://shop/room/index',
        'picture' => 'addon/live/shop/view/public/img/live.png',
        'parent' => 'TOOL_ROOT',
        'is_show' => 1,
        'sort' => 15,
        'child_list' => [
            [
                'name' => 'LIVE_ROOM',
                'title' => '直播间',
                'url' => 'live://shop/room/index',
                'is_show' => 1,
                'sort' => 1,
                'child_list' => [
                    [
                        'name' => 'ADD_LIVE_ROOM',
                        'title' => '添加直播间',
                        'url' => 'live://shop/room/add',
                        'is_show' => 0,
                        'sort' => 1,
                    ],
                    [
                        'name' => 'DELETE_LIVE_ROOM_',
                        'title' => '删除直播间',
                        'url' => 'live://shop/room/delete',
                        'is_show' => 0,
                        'sort' => 2,
                    ],
                    [
                        'name' => 'LIVE_ROOM_OPERATE',
                        'title' => '运营',
                        'url' => 'live://shop/room/operate',
                        'is_show' => 0,
                        'sort' => 3,
                    ],
                    [
                        'name' => 'SYNC_LIVE_ROOM',
                        'title' => '同步直播间',
                        'url' => 'live://shop/room/sync',
                        'is_show' => 0,
                        'sort' => 4,
                    ],
                    [
                        'name' => 'ADD_GOODS_TO_LIVE_ROOM',
                        'title' => '添加商品到直播间',
                        'url' => 'live://shop/room/addGoods',
                        'is_show' => 0,
                        'sort' => 5,
                    ]
                ]
            ],
            [
                'name' => 'LIVE_GOODS',
                'title' => '直播商品',
                'url' => 'live://shop/goods/index',
                'is_show' => 1,
                'sort' => 2,
                'child_list' => [
                    [
                        'name' => 'ADD_LIVE_GOODS',
                        'title' => '添加商品',
                        'url' => 'live://shop/goods/add',
                        'is_show' => 0,
                        'sort' => 1,
                    ],
                    [
                        'name' => 'DELETE_LIVE_GOODS',
                        'title' => '删除商品',
                        'url' => 'live://shop/goods/delete',
                        'is_show' => 0,
                        'sort' => 2,
                    ],
                    [
                        'name' => 'SYNC_LIVE_GOODS',
                        'title' => '同步直播商品库',
                        'url' => 'live://shop/goods/sync',
                        'is_show' => 0,
                        'sort' => 3,
                    ]
                ]
            ],
        ]
    ]
];