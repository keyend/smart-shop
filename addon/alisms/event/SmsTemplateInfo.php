<?php
namespace addon\alisms\event;
use addon\alisms\model\Config;
/**
 * 获取短信模板数据
 */
class SmsTemplateInfo
{
    /**
     * 获取短信模板数据
     */
    public function handle($param)
    {
        $config_model = new Config();
        $sms_config = $config_model->getSmsConfig($param['site_id'], 'shop');
        $sms_config = $sms_config[ 'data' ];
        if ($sms_config['is_use']) {
            $template_info = model('message_template')->getInfo([ ['keywords', '=', $param['keywords'] ]]);
            if (!empty($template_info['sms_json'])) {
                return json_decode($template_info['sms_json'], true);
            }
        }
    }
}