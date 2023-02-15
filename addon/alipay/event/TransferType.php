<?php
namespace addon\alipay\event;
use addon\alipay\model\Config;
class TransferType
{
    public function handle(array $param)
    {
        $app_type = isset($param['app_type']) ? $param['app_type'] : '';
        if (!empty($app_type)) {
            if (!in_array($app_type, ["h5", "app", "pc", "aliapp"])) {
                return '';
            }
            $config_model    = new Config();
            $config_result   = $config_model->getPayConfig($param['site_id']);
            $config          = $config_result["data"]["value"] ?? [];
            $transfer_status = $config["transfer_status"] ?? 0;
            if ($transfer_status == 0) {
                return '';
            }
        }
        $info = array(
            "type"      => "alipay",
            "type_name" => "支付宝",
        );
        return $info;
    }
}