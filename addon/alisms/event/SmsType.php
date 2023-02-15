<?php
namespace addon\alisms\event;
use addon\alisms\model\Config;
/**
 * 短信方式  (后台调用)
 */
class SmsType
{
    /**
     * 短信发送方式方式及配置
     */
    public function handle($param)
    {
        $info = array(
            "sms_type"      => "alisms",
            "sms_type_name" => "阿里云短信",
            "edit_url"      => "alisms://shop/sms/config",
            "shop_url"      => "alisms://shop/sms/config",
            "desc"          => "阿里云短信服务。"
        );
        $config_model   = new Config();
        $config         = $config_model->getSmsConfig($param['site_id']);
        $info['status'] = $config['data']['is_use'] ?? 0;
        return $info;
    }
}