<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //展示活动
        'ShowPromotion' => [
            'addon\notes\event\ShowPromotion',
        ],
        'PromotionType' => [
            'addon\notes\event\PromotionType',
        ],
    ],
    'subscribe' => [
    ],
];