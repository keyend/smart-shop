<?php
namespace addon\memberrecharge\event;
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
        return success();
    }
}