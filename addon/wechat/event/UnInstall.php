<?php
namespace addon\Wechat\event;
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
        return error('', 'System addon can not be uninstalled!');
    }
}