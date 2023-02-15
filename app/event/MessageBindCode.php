<?php
namespace app\event;
use app\model\member\Member;
/**
 * 绑定发送验证码
 */
class MessageBindCode
{
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "MEMBER_BIND") {
            $member_model = new Member();
            $result       = $member_model->bindCode($param);
            return $result;
        }
    }
}