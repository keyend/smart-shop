<?php
return [
    'template' => [
    ],
    'util'     => [
    ],
    'link'     => [
        [
            'name'       => 'THEMATIC_ACTIVITIES',
            'title'      => '专题活动',
            'parent'     => 'MARKETING_LINK',
            'wap_url'    => '',
            'web_url'    => '',
            'sort'       => 0,
            'child_list' => [
                [
                    'name'    => 'THEMATIC_ACTIVITIES_LIST',
                    'title'   => '专题活动列表',
                    'parent'  => '',
                    'wap_url' => '/promotionpages/topics/list/list',
                    'web_url' => '',
                    'sort'    => 0
                ]
            ]
        ]
    ],
];