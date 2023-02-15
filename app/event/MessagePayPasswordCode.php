<?php
namespace app\event;
use app\model\member\Member;
/**
 * 绑定发送验证码
 */
class MessagePayPasswordCode
{
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "MEMBER_PAY_PASSWORD") {
            $member_model = new Member();
            $result       = $member_model->paypasswordCode($param);
            return $result;
        }
    }
}