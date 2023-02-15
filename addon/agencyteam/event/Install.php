<?php
namespace addon\agencyteam\event;

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
        $site_id = request()->siteid();

        return success();
    }
}