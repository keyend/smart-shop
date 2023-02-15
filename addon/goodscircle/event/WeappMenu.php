<?php
namespace addon\goodscircle\event;
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
            'title'       => '微信圈子',
            'description' => '朋友间的好物分享',
            'url'         => 'goodscircle://shop/config/index',
            'icon'        => 'addon/goodscircle/icon.png'
        ];
        return $data;
    }
}