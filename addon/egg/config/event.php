<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //展示活动
        'ShowPromotion' => [
            'addon\egg\event\ShowPromotion',
        ],
        'PromotionType' => [
            'addon\egg\event\PromotionType',
        ],
        //关闭砸金蛋
        'CloseEgg'      => [
            'addon\egg\event\CloseEgg',
        ],
        //开启砸金蛋
        'OpenEgg'       => [
            'addon\egg\event\OpenEgg',
        ],
        'MemberAccountFromType' => [
            'addon\egg\event\MemberAccountFromType',
        ],
    ],
    'subscribe' => [
    ],
];