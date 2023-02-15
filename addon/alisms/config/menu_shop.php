<?php
return [
    [
        'name'           => 'ALI_SMS_CONFIG',
        'title'          => '阿里云短信配置',
        'url'            => 'alisms://shop/sms/config',
        'parent'         => 'SMS_MANAGE',
        'is_show'        => 0,
        'is_control'     => 1,
        'is_icon'        => 0,
        'picture'        => '',
        'picture_select' => '',
        'sort'           => 1,
    ],
    [
        'name'           => 'MESSAGE_SMS_EDIT',
        'title'          => '编辑阿里云短信模板',
        'url'            => 'alisms://shop/message/edit',
        'parent'         => 'MESSAGE_LISTS',
        'is_show'        => 0,
        'picture'        => '',
        'picture_select' => '',
        'sort'           => 1,
        'child_list'     => [
        ],
    ],
];