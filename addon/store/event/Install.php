<?php
namespace addon\store\event;
use app\model\system\Menu;
/**
 * 应用安装
 */
class Install
{
    /**
     * 执行安装
     */
    public function handle()
    {
        $menu = new Menu();
        $menu->refreshMenu("store", "store");
        return success();
    }
}