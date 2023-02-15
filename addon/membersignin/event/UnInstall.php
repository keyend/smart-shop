<?php
namespace addon\membersignin\event;
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
        return error('', "系统插件不允许删除");
    }
}