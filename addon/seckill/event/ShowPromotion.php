<?php
namespace addon\seckill\event;
/**
 * 活动展示
 */
class ShowPromotion
{
    /**
     * 活动展示
     *
     * @return multitype:number unknown
     */
    public function handle()
    {
        $data = [
            'shop' => [
                [
                    //插件名称
                    'name'        => 'seckill',
                    //展示分类（根据平台端设置，shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '秒杀',
                    //展示介绍
                    'description' => '限时抢购、特价促销，刺激粉丝下单消费',
                    //展示图标
                    'icon'        => 'addon/seckill/icon.png',
                    //跳转链接
                    'url'         => 'seckill://shop/seckill/goodslist',
					'sort'        => '5',
                ]
            ]
        ];
        return $data;
    }
}