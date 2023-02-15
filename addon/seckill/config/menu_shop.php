<?php
return [
    [
        'name'           => 'PROMOTION_SECKILL',
        'title'          => '秒杀',
        'url'            => 'seckill://shop/seckill/goodslist',
        'parent'         => 'TOOL_ROOT',
        'is_show'        => 1,
        'is_control'     => 1,
        'is_icon'        => 0,
        'picture'        => '',
        'picture_select' => '',
        'sort'           => 5,
        'child_list'     => [
            [
                'name' => 'PROMOTION_SECKILL_GOODS_LIST',
                'title' => '商品管理',
                'url' => 'seckill://shop/seckill/goodslist',
                'parent' => 'PROMOTION_CENTER',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'picture' => '',
                'picture_select' => '',
                'sort' => 100,
            ],
            [
                'name' => 'PROMOTION_SECKILL_TIME',
                'title' => '时段管理',
                'url' => 'seckill://shop/seckill/lists',
                'parent' => 'PROMOTION_CENTER',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'picture' => '',
                'picture_select' => '',
                'sort' => 100,
            ],
            [
                'name' => 'PROMOTION_SECKILL_ADD',
                'title' => '添加时间段',
                'url' => 'seckill://shop/seckill/add',
                'sort' => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_ADD',
                'title' => '编辑时间段',
                'url' => 'seckill://shop/seckill/edit',
                'sort' => 2,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_DELETE',
                'title' => '删除时间段',
                'url' => 'seckill://shop/seckill/delete',
                'sort' => 3,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS',
                'title' => '秒杀商品',
                'url' => 'seckill://shop/seckill/goods',
                'sort' => 4,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS_SELECT',
                'title' => '选择商品',
                'url' => 'seckill://shop/seckill/selectgoods',
                'sort' => 5,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS_ADD',
                'title' => '添加商品',
                'url' => 'seckill://shop/seckill/addgoods',
                'sort' => 6,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS_UPDATE',
                'title' => '编辑商品',
                'url' => 'seckill://shop/seckill/updategoods',
                'sort' => 7,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS_DELETE',
                'title' => '删除商品',
                'url' => 'seckill://shop/seckill/deletegoods',
                'sort' => 8,
                'is_show' => 0
            ],
        ]
    ],
];