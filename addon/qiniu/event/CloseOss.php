<?php
namespace addon\qiniu\event;
use addon\qiniu\model\Config;
/**
 * 关闭云上传
 */
class CloseOss
{
    public function handle()
    {
        $config_model = new Config();
        $result       = $config_model->modifyConfigIsUse(0);
        return $result;
    }
}