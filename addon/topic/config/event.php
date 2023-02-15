<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //专题活动开启事件
        'OpenTopic'          => [
            'addon\topic\event\OpenTopic',
        ],
        //专题活动关闭事件
        'CloseTopic'         => [
            'addon\topic\event\CloseTopic',
        ],
        //展示活动
        'ShowPromotion'      => [
            'addon\topic\event\ShowPromotion',
        ],
        //用于订单
        'PromotionType'      => [
            'addon\topic\event\PromotionType',
        ],
        // 商品营销活动类型
        'GoodsPromotionType' => [
            'addon\topic\event\GoodsPromotionType',
        ],
        // 商品营销活动信息
        'GoodsPromotion'     => [
            'addon\topic\event\GoodsPromotion',
        ],
        // 订单营销活动类型
        'OrderPromotionType' => [
            'addon\topic\event\OrderPromotionType',
        ],
    ],
    'subscribe' => [
    ],
];