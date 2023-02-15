<?php
namespace addon\topic\event;
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
                    'name'        => 'topic',
                    //展示分类（根据平台端设置，shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '专题活动',
                    //展示介绍
                    'description' => '专题活动，多元化营销',
                    //展示图标
                    'icon'        => 'addon/topic/icon.png',
                    //跳转链接
                    'url'         => 'topic://shop/topic/goodslist',
					'sort'        => '7',
                ]
            ]
        ];
        return $data;
    }
}