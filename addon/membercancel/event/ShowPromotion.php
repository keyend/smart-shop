<?php
namespace addon\membercancel\event;
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
                    'name'        => 'membercancel',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '会员注销',
                    //展示介绍
                    'description' => '会员注销',
                    //展示图标
                    'icon'        => 'addon/membercancel/icon.png',
                    //跳转链接
                    'url'         => 'membercancel://shop/membercancel/lists',
					'sort'        => '19',
                ]
            ]
        ];
        return $data;
    }
}