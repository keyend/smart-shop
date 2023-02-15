<?php
namespace addon\bundling\event;
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
        try {
            execute_sql('addon/bundling/data/uninstall.sql');
            return success();
        } catch (\Exception $e) {
            return error('', $e->getMessage());
        }
    }
}