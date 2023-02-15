<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //订单支付回调事件
        'MemberrechargeOrderPayNotify' => [
            'addon\memberrecharge\event\MemberrechargeOrderPayNotify',
        ],
        //关闭订单事件
        'MemberrechargeOrderClose'     => [
            'addon\memberrecharge\event\MemberrechargeOrderClose',
        ],
        'MemberAccountFromType' => [
            'addon\memberrecharge\event\MemberAccountFromType',
        ],
        'MemberAccountRule' => [
            'addon\memberrecharge\event\MemberAccountRule',
        ],
    ],
    'subscribe' => [
    ],
];