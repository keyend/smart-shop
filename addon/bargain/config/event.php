<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //展示活动
        'ShowPromotion' => [
            'addon\bargain\event\ShowPromotion',
        ],
        'PromotionType'      => [
            'addon\bargain\event\PromotionType',
        ],
        //关闭砍价
        'CloseBargain'       => [
            'addon\bargain\event\CloseBargain',
        ],
        //开启砍价
        'OpenBargain'        => [
            'addon\bargain\event\OpenBargain',
        ],
        // 关闭发起的砍价
        'BargainLaunchClose' => [
            'addon\bargain\event\BargainLaunchClose',
        ],
        // 商品营销活动类型
        'GoodsPromotionType' => [
            'addon\bargain\event\GoodsPromotionType',
        ],
        // 商品营销活动信息
        'GoodsPromotion'     => [
            'addon\bargain\event\GoodsPromotion',
        ],
        // 商品列表
        'GoodsListPromotion' => [
            'addon\bargain\event\GoodsListPromotion',
        ],
        // 订单营销活动类型
        'OrderPromotionType' => [
            'addon\bargain\event\OrderPromotionType',
        ],
    ],
    'subscribe' => [
    ],
];