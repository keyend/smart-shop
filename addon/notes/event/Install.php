<?php
namespace addon\notes\event;
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
        return success();
    }
}