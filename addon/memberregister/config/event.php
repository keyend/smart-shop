<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //会员行为事件
        'MemberAction'   => [
            'addon\memberregister\event\MemberAction',
        ],
        //会员注册奖励
        'MemberRegister' => [
            'addon\memberregister\event\MemberRegister',
        ],
        'MemberAccountFromType' => [
            'addon\memberregister\event\MemberAccountFromType',
        ],
        'MemberAccountRule' => [
            'addon\memberregister\event\MemberAccountRule',
        ],
    ],
    'subscribe' => [
    ],
];