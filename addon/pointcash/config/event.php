<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //展示活动
        'ShowPromotion'         => [
            'addon\pointcash\event\ShowPromotion',
        ],
        'MemberAccountFromType' => [
            'addon\pointcash\event\MemberAccountFromType',
        ],
    ],
    'subscribe' => [
    ],
];