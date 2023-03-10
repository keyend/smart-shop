<?php
return [
    [
        'name'       => 'PROMOTION_TURNTABLE',
        'title'      => '幸运抽奖',
        'url'        => 'turntable://shop/turntable/lists',
        'parent'     => 'TOOL_ROOT',
        'is_show'    => 1,
        'sort'       => 10,
        'child_list' => [
		
                    [
                        'name'    => 'PROMOTION_TURNTABLE_ADD',
                        'title'   => '添加活动',
                        'url'     => 'turntable://shop/turntable/add',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name'    => 'PROMOTION_TURNTABLE_EDIT',
                        'title'   => '编辑活动',
                        'url'     => 'turntable://shop/turntable/edit',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name'    => 'PROMOTION_TURNTABLE_DETAIL',
                        'title'   => '活动详情',
                        'url'     => 'turntable://shop/turntable/detail',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name'    => 'PROMOTION_TURNTABLE_DELETE',
                        'title'   => '删除活动',
                        'url'     => 'turntable://shop/turntable/delete',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name'    => 'PROMOTION_TURNTABLE_FINISH',
                        'title'   => '关闭活动',
                        'url'     => 'turntable://shop/turntable/finish',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name'    => 'PROMOTION_TURNTABLE_RECORD',
                        'title'   => '抽奖记录',
                        'url'     => 'turntable://shop/record/lists',
                        'sort'    => 1,
                        'is_show' => 0
                    ]

                ]

            ],

        ];