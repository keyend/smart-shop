<?php
namespace addon\weapp\event;
use addon\weapp\model\Config;
/**
 * api
 */
class ApiConfigChange
{
    /**
     * api配置变更
     */
    public function handle($param = [])
    {
        $config = new Config();
        $config->clearWeappVersion();
    }
}