<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //展示活动
        'ShowPromotion' => [
            'addon\groupbuy\event\ShowPromotion',
        ],
        'PromotionType'      => [
            'addon\groupbuy\event\PromotionType',
        ],
        //关闭团购
        'CloseGroupbuy'      => [
            'addon\groupbuy\event\CloseGroupbuy',
        ],
        //开启团购
        'OpenGroupbuy'       => [
            'addon\groupbuy\event\OpenGroupbuy',
        ],
        // 商品营销活动类型
        'GoodsPromotionType' => [
            'addon\groupbuy\event\GoodsPromotionType',
        ],
        // 商品营销活动信息
        'GoodsPromotion'     => [
            'addon\groupbuy\event\GoodsPromotion',
        ],
        // 商品列表
        'GoodsListPromotion' => [
            'addon\groupbuy\event\GoodsListPromotion',
        ],
        // 订单营销活动类型
        'OrderPromotionType' => [
            'addon\groupbuy\event\OrderPromotionType',
        ],
    ],
    'subscribe' => [
    ],
];