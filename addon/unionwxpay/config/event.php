<?php
// 事件定义文件
return [
    'bind' => [

    ],

    'listen' => [
        //支付异步回调
        'PayNotify'    => [
            'addon\unionwxpay\event\PayNotify'
        ],
        //支付方式，后台查询
        'PayType'      => [
            'addon\unionwxpay\event\PayType'
        ],
        //支付，前台应用
        'Pay'          => [
            'addon\unionwxpay\event\Pay'
        ],
        'PayClose'     => [
            'addon\unionwxpay\event\PayClose'
        ],
        'PayRefund'    => [
            'addon\unionwxpay\event\PayRefund'
        ],
        'PayTransfer'  => [
            'addon\unionwxpay\event\PayTransfer'
        ],
        'TransferType' => [
            'addon\unionwxpay\event\TransferType'
        ]
    ],

    'subscribe' => [
    ],
];
