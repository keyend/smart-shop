<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //限时折扣开启
        'OpenDiscount'       => [
            'addon\discount\event\OpenDiscount',
        ],
        //限时折扣关闭
        'CloseDiscount'      => [
            'addon\discount\event\CloseDiscount',
        ],
        // 商品营销活动类型
        'GoodsPromotionType' => [
            'addon\discount\event\GoodsPromotionType',
        ],
        // 商品营销活动信息
        'GoodsPromotion'     => [
            'addon\discount\event\GoodsPromotion',
        ],
    ],
    'subscribe' => [
    ],
];