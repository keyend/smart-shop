<?php
namespace app\event;
use app\model\system\H5;
/**
 * api配置变更
 */
class ApiConfigChange
{
    public function handle()
    {
        $h5 = new H5();
        $h5->refresh();
    }
}