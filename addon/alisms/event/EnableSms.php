<?php
namespace addon\alisms\event;
use addon\alisms\model\Config;
/**
 * 查询启用的短信插件
 */
class EnableSms
{
    /**
     * 短信发送方式方式及配置
     */
    public function handle($param)
    {
        $info = array (
            "sms_type" => "alisms",
            "sms_type_name" => "阿里云短信",
            "edit_url" => "alisms://shop/sms/config",
            "shop_url" => "alisms://shop/sms/config",
            "desc" => "阿里云短信服务。"
        );
        $config_model = new Config();
        $config = $config_model->getSmsConfig($param[ 'site_id' ]);
        if ($config[ 'data' ][ 'is_use' ] == 1) {
            return $info;
        }
    }
}