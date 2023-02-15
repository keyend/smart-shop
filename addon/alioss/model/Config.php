<?php
namespace addon\alioss\model;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
/**
 * 阿里云配置
 */
class Config extends BaseModel
{
    /**
     * 设置阿里云OSS上传配置
     * array $data
     */
    public function setAliossConfig($data, $status, $site_id = 1, $app_module = 'shop')
    {
        if ($status == 1) {
            event("CloseOss", []);//同步关闭所有云上传
        }
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '阿里云OSS上传配置', $status, [['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'ALIOSS_CONFIG']]);
        return $res;
    }
    /**
     * 获取阿里云上传配置
     */
    public function getAliossConfig($site_id = 1, $app_module = 'shop')
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'ALIOSS_CONFIG']]);
        return $res;
    }
    /**
     * 配置阿里云开关状态
     * @param $status
     */
    public function modifyConfigIsUse($status, $site_id = 1, $app_module = 'shop')
    {
        $config = new ConfigModel();
        $res    = $config->modifyConfigIsUse($status, [['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'ALIOSS_CONFIG']]);
        return $res;
    }
}