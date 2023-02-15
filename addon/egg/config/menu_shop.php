<?php
return [

    [
        'name'       => 'PROMOTION_EGG',
        'title'      => '砸金蛋',
        'url'        => 'egg://shop/egg/lists',
        'parent'     => 'TOOL_ROOT',
        'is_show'    => 1,
        'sort'       => 8,
        'child_list' => [

                    [
                        'name'    => 'PROMOTION_EGG_ADD',
                        'title'   => '添加活动',
                        'url'     => 'egg://shop/egg/add',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name'    => 'PROMOTION_EGG_EDIT',
                        'title'   => '编辑活动',
                        'url'     => 'egg://shop/egg/edit',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name'    => 'PROMOTION_EGG_DETAIL',
                        'title'   => '活动详情',
                        'url'     => 'egg://shop/egg/detail',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name'    => 'PROMOTION_EGG_DELETE',
                        'title'   => '删除活动',
                        'url'     => 'egg://shop/egg/delete',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name'    => 'PROMOTION_EGG_FINISH',
                        'title'   => '关闭活动',
                        'url'     => 'egg://shop/egg/finish',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name'    => 'PROMOTION_EGG_RECORD',
                        'title'   => '抽奖记录',
                        'url'     => 'egg://shop/record/lists',
                        'sort'    => 1,
                        'is_show' => 0
                ]
        ]
    ],
];