<?php
return [
    'bind' => [
    ],
    'listen' => [
        //支付异步回调
        'PayNotify'    => [
            'addon\alipay\event\PayNotify'
        ],
        //支付方式，后台查询
        'PayType'      => [
            'addon\alipay\event\PayType'
        ],
        //支付，前台应用
        'Pay'          => [
            'addon\alipay\event\Pay'
        ],
        'PayClose'     => [
            'addon\alipay\event\PayClose'
        ],
        'PayRefund'    => [
            'addon\alipay\event\PayRefund'
        ],
        'PayTransfer'  => [
            'addon\alipay\event\PayTransfer'
        ],
        'TransferType' => [
            'addon\alipay\event\TransferType'
        ]
    ],
    'subscribe' => [
    ],
];