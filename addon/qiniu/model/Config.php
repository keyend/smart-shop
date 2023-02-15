<?php
namespace addon\qiniu\model;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
/**
 * 七牛云配置
 */
class Config extends BaseModel
{
    /**
     * 设置七牛云上传配置
     * array $data
     */
    public function setQiniuConfig($data, $status, $site_id = 1, $app_module = 'shop')
    {
        if ($status == 1) {
            event("CloseOss", []);//同步关闭所有云上传
        }
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '七牛云存储配置', $status, [['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'QINIU_CONFIG']]);
        return $res;
    }
    /**
     * 获取七牛云上传配置
     */
    public function getQiniuConfig($site_id = 1, $app_module = 'shop')
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'QINIU_CONFIG']]);
        return $res;
    }
    /**
     * 配置七牛云开关状态
     * @param $status
     */
    public function modifyConfigIsUse($status)
    {
        $config = new ConfigModel();
        $res    = $config->modifyConfigIsUse($status, [['site_id', '=', 1], ['app_module', '=', 'shop'], ['config_key', '=', 'QINIU_CONFIG']]);
        return $res;
    }
}