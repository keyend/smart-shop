<?php
namespace app\model\express;
use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
use extend\Kdbird as KdbirdExtend;
/**
 * 快递鸟
 */
class Kdbird extends BaseModel
{
    /*********************************************************************** 快递100 start ***********************************************************************/
    /**
     * 快递鸟配置
     * @param $site_id
     * @return \multitype
     */
    public function getKdbirdConfig($site_id)
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['app_module', '=', 'shop'], ["site_id", "=", $site_id], ['config_key', '=', 'EXPRESS_KDBIRD_CONFIG']]);
        return $res;
    }
    /**
     * 设置物流配送配置
     * @param $data
     * @return \multitype
     */
    public function setKdbirdConfig($data, $is_use, $site_id)
    {
        if ($is_use > 0) {
            event("CloseTrace", ['site_id' => $site_id]);
        }
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '快递鸟设置', $is_use, [['app_module', '=', 'shop'], ["site_id", "=", $site_id], ['config_key', '=', 'EXPRESS_KDBIRD_CONFIG']]);
        return $res;
    }
    /**
     * 开关状态
     * @param $is_use
     * @return array
     */
    public function modifyStatus($is_use, $site_id)
    {
        $config = new ConfigModel();
        $res    = $config->modifyConfigIsUse($is_use, [['app_module', '=', 'shop'], ["site_id", "=", $site_id], ['config_key', '=', 'EXPRESS_KDBIRD_CONFIG']]);
        return $res;
    }
    /*********************************************************************** 快递100 end ***********************************************************************/
    /**
     * 查询物流轨迹 并且转化为兼容数据结构
     * @param $code
     * @param $express_no
     * @return array
     */
    public function trace($code, $express_no, $site_id)
    {
        $config_result = $this->getKdbirdConfig($site_id);
        $config        = $config_result["data"];
        if ($config["is_use"] == 0) return $this->error();
        $kd100_extend = new KdbirdExtend($config["value"]);
        $result       = $kd100_extend->orderTracesSubByJson($express_no, $code);
        if (isset($result['success']) && $result['success']) {
            return $this->success($result);
        } else {
            return $this->error($result, $result['reason']);
        }
    }
}