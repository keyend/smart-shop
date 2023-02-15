<?php
namespace addon\membersignin\event;
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
                    'name'        => 'membersignin',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '积分签到',
                    //展示介绍
                    'description' => '培养用户登录习惯，提高用户日常活跃',
                    //展示图标
                    'icon'        => 'addon/membersignin/icon.png',
                    //跳转链接
                    'url'         => 'membersignin://shop/config/index',
					'sort'        => '12',
                ]
            ]
        ];
        return $data;
    }
}