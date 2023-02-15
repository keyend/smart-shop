<?php
namespace app\event;
use app\model\member\Login;
/**
 * 登录成功发送通知
 */
class MessageLogin
{
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "LOGIN") {
            $login_model = new Login();
            $result      = $login_model->loginSuccess($param);
            return $result;
        }
    }
}