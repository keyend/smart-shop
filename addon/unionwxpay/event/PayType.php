<?php
namespace addon\unionwxpay\event;
use addon\unionwxpay\model\Config;
/**
 * 支付方式  (前后台调用)
 */
class PayType
{
    /**
     * 支付方式及配置
     */
    public function handle($params)
    {
        $app_type = isset($params['app_type']) ? $params['app_type'] : '';
        if (!empty($app_type)) {
            $config_model   = new Config();
            $app_type_array = ['wechat','weapp'];
            if (!in_array($app_type, $app_type_array)) {
                return '';
            }
            $config_result = $config_model->getPayConfig($params['site_id']);
            $config        = $config_result["data"]["value"] ?? [];
            $pay_status    = $config["pay_status"] ?? 0;
            if ($pay_status == 0) {
                return '';
            }
        }
        $info = array(
            "pay_type"      => "unionwxpay",
            "pay_type_name" => "微信支付-银联通道",
            "edit_url"      => "unionwxpay://shop/pay/config",
            "shop_url"      => "unionwxpay://shop/pay/config",
            "logo"          => "addon/unionwxpay/icon.png",
            "desc"          => "银联支付，用户通过微信内置浏览器或小程序打开商品页面购买，调起微信支付模块完成支付。"
        );
        return $info;
    }
}