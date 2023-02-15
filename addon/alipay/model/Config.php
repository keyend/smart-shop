<?php
namespace addon\alipay\model;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
/**
 * 支付宝支付配置
 */
class Config extends BaseModel
{
    /**
     * 设置支付配置
     * array $data
     */
    public function setPayConfig($data, $site_id = 0, $app_module = 'shop')
    {
        $config = new ConfigModel();
        $res = $config->setConfig($data, '支付宝支付配置', 1, [ [ 'site_id', '=', $site_id ], [ 'app_module', '=', $app_module ], [ 'config_key', '=', 'ALI_PAY_CONFIG' ] ]);
        return $res;
    }
    /**
     * 获取支付配置
     */
    public function getPayConfig($site_id = 0, $app_module = 'shop')
    {
        $config = new ConfigModel();
        $res = $config->getConfig([ [ 'site_id', '=', $site_id ], [ 'app_module', '=', $app_module ], [ 'config_key', '=', 'ALI_PAY_CONFIG' ] ]);
        return $res;
    }
}