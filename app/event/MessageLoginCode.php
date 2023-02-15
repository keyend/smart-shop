<?php
namespace app\event;
use app\model\member\Login;
/**
 * 登录发送验证码
 */
class MessageLoginCode
{
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "LOGIN_CODE") {
            $login_model = new Login();
            $result      = $login_model->loginCode($param);
            return $result;
        }
    }
}