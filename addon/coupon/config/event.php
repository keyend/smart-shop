<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //优惠券自动关闭
        'CronCouponEnd'     => [
            'addon\coupon\event\CronCouponEnd',
        ],
        // 优惠券活动定时结束
        'CronCouponTypeEnd' => [
            'addon\coupon\event\CronCouponTypeEnd',
        ]
    ],
    'subscribe' => [
    ],
];