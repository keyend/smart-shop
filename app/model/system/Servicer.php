<?php
namespace app\model\system;
use app\model\BaseModel;
use app\model\system\Config as ConfigModel;
/**
 * 客服配置
 */
class Servicer extends BaseModel
{
    /**
     * 设置客服配置
     * array $data
     */
    public function setServicerConfig($data)
    {
        $config_model = new ConfigModel();
        $res = $config_model->setConfig($data, '客服配置', 1, [['site_id', '=', 0], ['app_module', '=', 'shop'], ['config_key', '=', 'SRRVICER_ROOT_CONFIG']]);
        return $res;
    }
    /**
     * 获取客服配置
     */
    public function getServicerConfig()
    {
        $config_model = new ConfigModel();
        $res = $config_model->getConfig([['site_id', '=', 0], ['app_module', '=', 'shop'], ['config_key', '=', 'SRRVICER_ROOT_CONFIG']]);
        return $res;
    }
}