<?php
namespace addon\system\Wechat\event;
use addon\system\Wechat\common\model\WechatMessage;
class CronMessageSend
{
    /**
     * 邮箱消息延时发送
     * @param array $param
     */
    public function handle($param = [])
    {
        $wechat_message = new WechatMessage();
        $res            = $wechat_message->cronMessageSend($param);
        return $res;
    }
}