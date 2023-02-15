<?php
return [
    'template' => [
    ],
    'util' => [
        [
            'name' => 'NOTES_LIST',
            'title' => '店铺笔记',
            'type' => 'SYSTEM',
            'controller' => 'Notes',
            'value' => '{"sources" : "default", "goodsId": [], style: 1, "backgroundColor": "", "padding": 10, "goodsCount": "3", "paddingLeftRight": 0, "notesLabel": 1, "readNum": 1, "thumbsUpNum": 1, uploadTime: 1, "list": {"imageUrl": "","title": "店铺笔记"}, "listMore": {"imageUrl": "","title": "查看更多"}, "titleTextColor": "#333333", "defaultTitleTextColor": "#333333", "moreTextColor": "#858585", "defaultMoreTextColor": "#858585"}',
            'sort' => '10024',
            'support_diy_view' => '',
            'max_count' => 1,
            'icon' => 'addon/notes/component/view/notes/img/icon/notes.png',
            'icon_selected' => 'addon/notes/component/view/notes/img/icon/notes_selected.png'
        ]
    ],
    'link' => [
        [
            'name' => 'NOTES',
            'title' => '店铺笔记',
            'parent' => 'MARKETING_LINK',
            'wap_url' => '',
            'web_url' => '',
            'sort' => 0,
            'child_list' => [
                [
                    'name' => 'NOTES_PREFECTURE',
                    'title' => '店铺笔记',
                    'parent' => '',
                    'wap_url' => '/promotionpages/notes/list/list',
                    'web_url' => '',
                    'sort' => 0
                ]
            ]
        ]
    ],
];