<?php
namespace addon\system\Wechat\event;
use liliuwei\think\Jump;
use addon\system\Wechat\common\model\WechatMessage as WechatMessageModel;
class WechatMessage
{
    use Jump;
    /**
     * 微信模板消息
     * @param array $param
     */
    public function handle($param = [])
    {
        $wechat_message = new WechatMessageModel();
        $res            = $wechat_message->sendMessage($param);
        return $res;
    }
}