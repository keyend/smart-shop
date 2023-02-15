<?php
namespace addon\egg\event;
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
                    'name'        => 'egg',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '砸金蛋',
                    //展示介绍
                    'description' => '消费积分砸金蛋，多元化奖品',
                    //展示图标
                    'icon'        => 'addon/egg/icon.png',
                    //跳转链接
                    'url'         => 'egg://shop/egg/lists',
					'sort'        => '8',
                ]
            ]
        ];
        return $data;
    }
}