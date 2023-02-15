<?php
namespace addon\ttapp\model;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
use think\facade\Cache;
/**
 * 小程序配置
 */
class Config extends BaseModel
{
    /******************************************************************** 小程序配置 start ****************************************************************************/
    /**
     * 设置小程序配置
     * @return multitype:string mixed
     */
    public function setConfig($data, $is_use, $site_id = 0)
    {
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '小程序设置', $is_use, [['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'TTAPP_CONFIG']]);
        return $res;
    }
    /**
     * 获取小程序配置信息
     * @return multitype:string mixed
     */
    public function getConfig($site_id = 0)
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'TTAPP_CONFIG']]);
        return $res;
    }
    /******************************************************************** 小程序配置 end ****************************************************************************/
    /**
     * 设置小程序版本信息
     * @param $data
     * @param $is_use
     * @param int $site_id
     * @return array
     */
    public function setVersion($data, $is_use, $site_id = 0)
    {
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '小程序版本', $is_use, [['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'TTAPP_VERSION']]);
        return $res;
    }
    /**
     * 获取小程序版本信息
     * @param int $site_id
     * @return array
     */
    public function getVersion($site_id = 0)
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'TTAPP_VERSION']]);
        return $res;
    }
    /**
     * 清除小程序版本信息
     */
    public function clearVersion()
    {
        model('config')->update(['value' => ''], [['app_module', '=', 'shop'], ['config_key', '=', 'TTAPP_VERSION']]);
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
        $config = model('config')->getInfo([ ['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'TTAPP_SHARE']], 'value');
        if (!empty($config) && !empty($config['value'])) $data = json_decode($config['value'], true);
        $data[$key] = $value;
        $model = new ConfigModel();
        $res = $model->setConfig($data, '小程序分享', 1, [['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'TTAPP_SHARE']]);
        return $res;
    }
    /**
     * 获取小程序分享配置
     * @param $site_id
     * @param $app_module
     */
    public function getShareConfig($site_id, $app_module){
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'TTAPP_SHARE']]);
        return $res;
    }
}