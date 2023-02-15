<?php
namespace addon\live\event;
/**
 * 小程序菜单
 */
class WeappMenu
{
    /**
     * 小程序菜单
     *
     * @return multitype:number unknown
     */
    public function handle()
    {
        $data = [
            'title'       => '小程序直播',
            'description' => '在小程序中实现直播互动与商品销售闭环',
            'url'         => 'live://shop/room/index',
            'icon'        => 'addon/live/icon.png'
        ];
        return $data;
    }
}