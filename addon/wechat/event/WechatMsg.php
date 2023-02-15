<?php
namespace addon\system\Wechat\event;
use addon\system\Wechat\common\model\Wechat;
class WechatMsg
{
    public function handle($param = [])
    {
        $wechat_config = new Wechat();
        $res           = $wechat_config->sendTemplateMsg(request()->siteid(), $param);
        return $res;
    }
}