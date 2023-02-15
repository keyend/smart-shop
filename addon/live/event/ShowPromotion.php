<?php
namespace addon\live\event;
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
                    'name'        => 'live',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '小程序直播',
                    //展示介绍
                    'description' => '助力商家，顺应时代趋势，打造商家私域流量',
                    //展示图标
                    'icon'        => 'addon/live/icon.png',
                    //跳转链接
                    'url'         => 'live://shop/room/index',
					'sort'        => '15',
                ]
            ]
        ];
        return $data;
    }
}