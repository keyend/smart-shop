<?php
namespace app\event;
use app\model\message\Message;
/**
 * 登录成功发送通知
 */
class MemberLogin
{
    public function handle($param)
    {
        // 发送通知
        $message_model = new Message();
        $message_model->sendMessage(["keywords" => "LOGIN", "member_id" => $param["member_id"], "site_id" => $param["site_id"]]);
    }
}