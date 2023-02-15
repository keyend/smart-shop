<?php
// 事件定义文件
return [
    'bind' => [
    ],
    'listen' => [
        //展示活动
        'ShowPromotion' => [
            'addon\fenxiao\event\ShowPromotion',
        ],
        'PromotionType'     => [
            'addon\fenxiao\event\PromotionType',
        ],
        'OrderComplete'     => [
            'addon\fenxiao\event\OrderSettlement',
        ],
        'OrderRefundFinish' => [
            'addon\fenxiao\event\OrderGoodsRefund',
        ],
        'OrderPay'          => [
            'addon\fenxiao\event\OrderPay',
        ],
        'MemberAccountFromType' => [
            'addon\fenxiao\event\MemberAccountFromType',
        ],
        'MemberRegister'     => [
            'addon\fenxiao\event\MemberRegister',
        ],
        'FenxiaoUpgrade'     => [
            'addon\fenxiao\event\FenxiaoUpgrade',
        ],
        'AddSite'            => [
            'addon\fenxiao\event\AddSiteDiyView',//增加默认自定义数据：主页主页、商品分类、底部导航
            'addon\fenxiao\event\AddSiteFenxiaoLevel',//增加默认分销等级：普通分销商
        ],
        // 商品列表
        'GoodsListPromotion' => [
            'addon\fenxiao\event\GoodsListPromotion',
        ],
        // 会员注销
        'MemberCancel' => [
            'addon\fenxiao\event\MemberCancel',
        ],
    ],
    'subscribe' => [
    ],
];