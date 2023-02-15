<?php
namespace addon\bundling\event;
/**
 * 店铺活动
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
                    'name'        => 'bundling',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type'   => 'shop',
                    //展示主题
                    'title'       => '套餐管理',
                    //展示介绍
                    'description' => '套餐活动功能',
                    //展示图标
                    'icon'        => 'addon/bundling/icon.png',
                    //跳转链接
                    'url'         => 'bundling://shop/bundling/lists',
                ]
            ]
        ];
        return $data;
    }
}