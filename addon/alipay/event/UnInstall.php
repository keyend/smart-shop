<?php
namespace addon\alipay\event;
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
        return error(-1, "系统插件不得删除");
    }
}