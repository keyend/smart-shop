<?php
namespace addon\fenxiao\event;
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
                    'name'        => 'fenxiao',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '全网分销',
                    //展示介绍
                    'description' => '全网分销，下线下单上线返佣',
                    //展示图标
                    'icon'        => 'addon/fenxiao/icon.png',
                    //跳转链接
                    'url'         => 'fenxiao://shop/fenxiao/index',
					'sort'        => '1',
                ]
            ]
        ];
        return $data;
    }
}