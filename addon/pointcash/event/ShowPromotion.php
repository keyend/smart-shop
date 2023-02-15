<?php
namespace addon\pointcash\event;
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
            'admin' => [
            ],
            'shop'  => [
                [
                    //插件名称
                    'name'        => 'pointcash',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '积分抵现',
                    //展示介绍
                    'description' => '下单时积分可抵部分现金',
                    //展示图标
                    'icon'        => 'addon/pointcash/icon.png',
                    //跳转链接
                    'url'         => 'pointcash://shop/config/index',
					'sort'        => '11',
                ],
            ],
        ];
        return $data;
    }
}