<?php
declare(strict_types=1);
namespace addon\servicer\event;
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
	    // try{
	    //     execute_sql('addon/manjian/data/install.sql');
	    //     return success();
	    // }catch (\Exception $e)
	    // {
	    //     return error('', $e->getMessage());
        // }
        
        return success();
    }
}