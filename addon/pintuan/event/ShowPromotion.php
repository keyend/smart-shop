<?php
namespace addon\pintuan\event;
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
                    'name'        => 'pintuan',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '拼团',
                    //展示介绍
                    'description' => '裂变式推广，促进交易达成',
                    //展示图标
                    'icon'        => 'addon/pintuan/icon.png',
                    //跳转链接
                    'url'         => 'pintuan://shop/pintuan/lists',
					'sort'        => '3',
                ]
            ]
        ];
        return $data;
    }
}