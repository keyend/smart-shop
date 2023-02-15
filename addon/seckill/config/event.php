<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //秒杀开启
        'OpenSeckill'        => [
            'addon\seckill\event\OpenSeckill',
        ],
        //秒杀关闭
        'CloseSeckill'       => [
            'addon\seckill\event\CloseSeckill',
        ],
        //展示活动
        'ShowPromotion'      => [
            'addon\seckill\event\ShowPromotion',
        ],
        'PromotionType'      => [
            'addon\seckill\event\PromotionType',
        ],
        // 商品营销活动类型
        'GoodsPromotionType' => [
            'addon\seckill\event\GoodsPromotionType',
        ],
        // 商品营销活动信息
        'GoodsPromotion'     => [
            'addon\seckill\event\GoodsPromotion',
        ],
        // 订单营销活动类型
        'OrderPromotionType' => [
            'addon\seckill\event\OrderPromotionType',
        ],
    ],
    'subscribe' => [
    ],
];