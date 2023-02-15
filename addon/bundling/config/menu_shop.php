<?php
return [
    [
        'name'           => 'PROMOTION_BUNDLING',
        'title'          => '套餐管理',
        'url'            => 'bundling://shop/bundling/lists',
        'parent'         => 'PROMOTION_ROOT',
        'is_show'        => 1,
        'is_control'     => 1,
        'is_icon'        => 0,
        'picture'        => '',
        'picture_select' => '',
        'sort'           => 5,
        'child_list'     => [
            [
                'name'    => 'PROMOTION_BUNDLING_DETAIL',
                'title'   => '套餐详情',
                'url'     => 'bundling://shop/bundling/detail',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name'    => 'PROMOTION_BUNDLING_ADD',
                'title'   => '添加套餐',
                'url'     => 'bundling://shop/bundling/add',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name'    => 'PROMOTION_BUNDLING_EDIT',
                'title'   => '编辑套餐',
                'url'     => 'bundling://shop/bundling/edit',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name'    => 'PROMOTION_BUNDLING_DELETE',
                'title'   => '删除套餐',
                'url'     => 'bundling://shop/bundling/delete',
                'sort'    => 1,
                'is_show' => 0
            ],
        ]
    ],
];