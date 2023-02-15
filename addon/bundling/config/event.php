<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        'PromotionType'      => [
            'addon\bundling\event\PromotionType',
        ],
        // 订单营销活动类型
        'OrderPromotionType' => [
            'addon\bundling\event\OrderPromotionType',
        ],
    ],
    'subscribe' => [
    ],
];