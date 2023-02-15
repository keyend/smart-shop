<?php
namespace app\event;
use app\model\express\Config as ConfigModel;
/**
 * 增加默认配送管理数据
 */
class AddSiteDelivery
{
    public function handle($param)
    {
        if (!empty($param['site_id'])) {
            $config_model = new ConfigModel();
            $data         = array();
            $res          = $config_model->setExpressConfig($data, 1, $param['site_id']);
            return $res;
        }
    }
}