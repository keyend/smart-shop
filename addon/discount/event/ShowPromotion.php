<?php
namespace addon\discount\event;
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
                    'name'        => 'discount',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'shop',
                    //展示主题
                    'title'       => '限时折扣',
                    //展示介绍
                    'description' => '限时折扣功能',
                    //展示图标
                    'icon'        => 'addon/discount/icon.png',
                    //跳转链接
                    'url'         => 'discount://shop/discount/lists',
                ]
            ]
        ];
        return $data;
    }
}