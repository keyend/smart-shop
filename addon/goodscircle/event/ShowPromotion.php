<?php
namespace addon\goodscircle\event;
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
                    'name'        => 'goodscircle',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '好物圈',
                    //展示介绍
                    'description' => '朋友间的好物分享，记录美好生活',
                    //展示图标
                    'icon'        => 'addon/goodscircle/icon.png',
                    //跳转链接
                    'url'         => 'goodscircle://shop/config/index',
					'sort'        => '16',
                ]
            ]
        ];
        return $data;
    }
}