<?php
namespace addon\memberconsume\event;
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
                    'name'        => 'memberconsume',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type'   => 'member',
                    //展示主题
                    'title'       => '消费奖励',
                    //展示介绍
                    'description' => '会员消费后奖励设置',
                    //展示图标
                    'icon'        => 'addon/memberconsume/icon.png',
                    //跳转链接
                    'url'         => 'memberconsume://shop/config/index',
                ]
            ]
        ];
        return $data;
    }
}