<?php
namespace app\event;
use app\model\member\Member;
/**
 * 找回密码发送验证码
 */
class MessageFindCode
{
    public function handle($param)
    {
        if ($param["keywords"] == "FIND_PASSWORD") {
            $member_model = new Member();
            $result       = $member_model->findCode($param);
            return $result;
        }
    }
}