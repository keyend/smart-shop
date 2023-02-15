<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        'ShowPromotion' => [
            'addon\agencyteam\event\ShowPromotion',
        ],
        'OrderComplete'     => [
            'addon\agencyteam\event\OrderSettlement',
        ],
        'PromotionType'     => [
            'addon\agencyteam\event\PromotionType',
        ],
        'MemberRegister'     => [
            'addon\agencyteam\event\MemberRegister',
        ],
        'UpdateMemberLevel'     => [
            'addon\agencyteam\event\UpdateMemberLevel',
        ],
        'MemberCancel' => [
            'addon\agencyteam\event\MemberCancel',
        ],
        'MemberChange' => [
            'addon\agencyteam\event\MemberChange',
        ]
    ],
    'subscribe' => [
    ],
];