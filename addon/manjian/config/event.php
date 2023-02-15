<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //满减开启
        'OpenManjian'   => [
            'addon\manjian\event\OpenManjian',
        ],
        //满减关闭
        'CloseManjian'  => [
            'addon\manjian\event\CloseManjian',
        ],
        // 订单完成
        'OrderComplete' => [
            'addon\manjian\event\OrderComplete'
        ],
        'MemberAccountFromType' => [
            'addon\manjian\event\MemberAccountFromType',
        ],
    ],
    'subscribe' => [
    ],
];