<?php
namespace addon\groupbuy\event;
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
                    'name'        => 'groupbuy',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '团购',
                    //展示介绍
                    'description' => '团购促进顾客增购，提升店铺销量。',
                    //展示图标
                    'icon'        => 'addon/groupbuy/icon.png',
                    //跳转链接
                    'url'         => 'groupbuy://shop/groupbuy/lists',
					'sort'        => '6',
                ]
            ]
        ];
        return $data;
    }
}