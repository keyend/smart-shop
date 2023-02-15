<?php
namespace addon\system\Wechat\event;
use addon\system\Wechat\common\model\Wechat;
class GetOAuthLoginType
{
    /**
     * 获取第三方登录类型，用于前端调用第三方登录相关类型，注意端口配置
     * @param array $param
     */
    public function handle($param = [])
    {
        $config_model  = new Wechat();
        $config_result = $config_model->getWechatConfigInfo($param['site_id']);
        if ($config_result["data"]["status"] == 1) {
            $config_info          = $this->info;
            $config_info['title'] = "微信公众号登录";
            $config_info['icon']  = __ROOT__ . '/addon/system/Wechat/sitehome/view/public/img/wechat.png';
            $config_info['cn']    = '微信公众号';
            $config_info['en']    = 'wechat';
            return $config_info;
        }
    }
}