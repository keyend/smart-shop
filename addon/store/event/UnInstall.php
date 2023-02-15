<?php
namespace addon\store\event;
use addon\store\model\Menu;
/**
 * 应用卸载
 */
class UnInstall
{
    /**
     * 执行卸载
     */
    public function handle()
    {
        $menu = new Menu();
        $res  = $menu->uninstall();
        return $res;
    }
}