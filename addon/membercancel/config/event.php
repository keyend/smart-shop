<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //展示活动
        'ShowPromotion'     => [
            'addon\membercancel\event\ShowPromotion',
        ],
        'PromotionType'      => [
            'addon\membercancel\event\PromotionType',
        ],
    ],
    'subscribe' => [
    ],
];