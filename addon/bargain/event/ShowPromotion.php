<?php
namespace addon\bargain\event;
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
                    'name'        => 'bargain',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '砍价',
                    //展示介绍
                    'description' => '邀请好友砍价，病毒式宣传，低成本获客',
                    //展示图标
                    'icon'        => 'addon/bargain/icon.png',
                    //跳转链接
                    'url'         => 'bargain://shop/bargain/lists',
					'sort'        => '4',
                ]
            ]
        ];
        return $data;
    }
}