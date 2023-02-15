<?php
namespace addon\manjian\event;
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
                    'name'        => 'manjian',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'shop',
                    //展示主题
                    'title'       => '满减活动',
                    //展示介绍
                    'description' => '满减送活动',
                    //展示图标
                    'icon'        => 'addon/manjian/icon.png',
                    //跳转链接
                    'url'         => 'manjian://shop/manjian/lists',
                ]
            ]
        ];
        return $data;
    }
}