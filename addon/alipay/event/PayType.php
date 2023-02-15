<?php
namespace addon\alipay\event;
use addon\alipay\model\Config;
/**
 * 支付方式  (后台调用)
 */
class PayType
{
    /**
     * 支付方式及配置
     */
    public function handle($param)
    {
        $app_type = isset($param['app_type']) ? $param['app_type'] : '';
        if (!empty($app_type)) {
            if (!in_array($app_type, ["h5", "app", "pc", "aliapp"])) {
                return '';
            }
            $config_model = new Config();
            $config_result = $config_model->getPayConfig($param['site_id']);
            $config        = $config_result["data"]["value"] ?? [];
            $pay_status    = $config["pay_status"] ?? 0;
            if ($pay_status == 0) {
                return '';
            }
        }
        $info = array(
            "pay_type"      => "alipay",
            "pay_type_name" => "支付宝支付",
            "edit_url"      => "alipay://shop/pay/config",
            "shop_url"      => "alipay://shop/pay/config",
            "logo"          => "addon/alipay/icon.png",
            "desc"          => "支付宝网站(www.alipay.com) 是国内先进的网上支付平台。"
        );
        return $info;
    }
}