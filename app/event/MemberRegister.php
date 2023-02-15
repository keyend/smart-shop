<?php
namespace app\event;
use app\model\message\Message;
/**
 * 注册成功发送通知
 */
class MemberRegister
{
    public function handle($param)
    {
        // 发送通知
        $message_model = new Message();
        $message_model->sendMessage(["keywords" => "REGISTER_SUCCESS", "member_id" => $param["member_id"], 'site_id' => $param['site_id']]);
    }
}