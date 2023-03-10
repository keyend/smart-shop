<?php
namespace addon\weapp\model;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
use think\facade\Cache;
/**
 * 微信小程序配置
 */
class Config extends BaseModel
{
    /******************************************************************** 微信小程序配置 start ****************************************************************************/
    /**
     * 设置微信小程序配置
     * @return multitype:string mixed
     */
    public function setWeappConfig($data, $is_use, $site_id = 0)
    {
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '微信小程序设置', $is_use, [['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'WEAPP_CONFIG']]);
        return $res;
    }
    /**
     * 获取微信小程序配置信息
     * @return multitype:string mixed
     */
    public function getWeappConfig($site_id = 0)
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'WEAPP_CONFIG']]);
        return $res;
    }
    /******************************************************************** 微信小程序配置 end ****************************************************************************/
    /**
     * 设置小程序版本信息
     * @param $data
     * @param $is_use
     * @param int $site_id
     * @return array
     */
    public function setWeappVersion($data, $is_use, $site_id = 0)
    {
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '小程序版本', $is_use, [['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'WEAPP_VERSION']]);
        return $res;
    }
    /**
     * 获取小程序版本信息
     * @param int $site_id
     * @return array
     */
    public function getWeappVersion($site_id = 0)
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'WEAPP_VERSION']]);
        return $res;
    }
    /**
     * 清除小程序版本信息
     */
    public function clearWeappVersion()
    {
        model('config')->update(['value' => ''], [['app_module', '=', 'shop'], ['config_key', '=', 'WEAPP_VERSION']]);
        Cache::tag("config")->clear();
    }
    /**
     * 设置小程序分享
     * @param $site_id
     * @param $app_module
     * @param $key
     * @param $value
     */
    public function setShareConfig($site_id, $app_module, $key, $value){
        $config = model('config')->getInfo([ ['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'WEAPP_SHARE']], 'value');
        if (!empty($config) && !empty($config['value'])) $data = json_decode($config['value'], true);
        $data[$key] = $value;
        $model = new ConfigModel();
        $res = $model->setConfig($data, '小程序分享', 1, [['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'WEAPP_SHARE']]);
        return $res;
    }
    /**
     * 获取小程序分享配置
     * @param $site_id
     * @param $app_module\
     */
    public function getShareConfig($site_id, $app_module){
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'WEAPP_SHARE']]);
        return $res;
    }
}