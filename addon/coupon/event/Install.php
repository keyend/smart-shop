<?php
namespace addon\coupon\event;
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
        try {
            execute_sql('addon/coupon/data/install.sql');
            return success();
        } catch (\Exception $e) {
            return error('', $e->getMessage());
        }
    }
}