<?php
namespace addon\cards\event;
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
                    'name'        => 'cards',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type'   => 'tool',
                    //展示主题
                    'title'       => '刮刮乐',
                    //展示介绍
                    'description' => '消费积分刮刮卡，多元化奖品',
                    //展示图标
                    'icon'        => 'addon/cards/icon.png',
                    //跳转链接
                    'url'         => 'cards://shop/cards/lists',
					'sort'        => '9',
                ]
            ]
        ];
        return $data;
    }
}