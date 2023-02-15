<?php
namespace addon\pointcash\model;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
/**
 * 微信小程序配置
 */
class Config extends BaseModel
{
    /**
     * 积分抵现设置
     * @param $data
     * @param $site_id
     * @param string $app_module
     * @return array
     */
    public function setPointCashConfig($data, $site_id, $app_module = 'shop')
    {
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '积分抵现', 1, [['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'POINTCASH_CONFIG']]);
        return $res;
    }
    /**
     * 获取积分抵现配置
     * @param $site_id
     * @param string $app_module
     * @return array
     */
    public function getPointCashConfig($site_id, $app_module = 'shop')
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', $app_module], ['config_key', '=', 'POINTCASH_CONFIG']]);
        if (empty($res['data']['value'])) {
            //默认值设置
            $res['data']['value'] = [
                'is_enable'    => 0, // 是否开启
                'cash_rate'    => 0, // 抵现比率
                'is_limit'     => 0, // 是否限制订单金额门槛
                'limit'        => 0.00, // 订单金额门槛
                'is_limit_use' => 0, // 是否限制使用上限
                'type'         => 0, // 限制类型 0：固定金额 1：订单百分比
                'max_use'      => 0 // 最大可用
            ];
        }
        return $res;
    }
}