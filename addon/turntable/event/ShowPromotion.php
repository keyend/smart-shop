<?php
namespace addon\turntable\event;
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
                    'name'        => 'turntable',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '幸运抽奖',
                    //展示介绍
                    'description' => '幸运抽奖，增强粘度，中奖概率轻松定',
                    //展示图标
                    'icon'        => 'addon/turntable/icon.png',
                    //跳转链接
                    'url'         => 'turntable://shop/turntable/lists',
					'sort'        => '10',
                ]
            ]
        ];
        return $data;
    }
}