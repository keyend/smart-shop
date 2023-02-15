<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //短信方式
        'SmsType'          => [
            'addon\alisms\event\SmsType'
        ],
        'DoEditSmsMessage' => [
            'addon\alisms\event\DoEditSmsMessage'
        ],
        'SendSms'          => [
            'addon\alisms\event\SendSms'
        ],
        //启用回调，使用这个短信，就要关闭其他短信插件
        'EnableCallBack'          => [
            'addon\alisms\event\EnableCallBack'
        ],
        //查询启用的短信插件
        'EnableSms' => [
            'addon\alisms\event\EnableSms'
        ],
        'SmsTemplateInfo' => [
            'addon\alisms\event\SmsTemplateInfo'
        ]
    ],
    'subscribe' => [
    ],
];