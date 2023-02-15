<?php
namespace addon\discount\event;
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
        /* 	    try{
                    execute_sql('addon/discount/data/install.sql');
                    return success();
                }catch (\Exception $e)
                {
                    return error('', $e->getMessage());
                } */
        return success();
    }
}