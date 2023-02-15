<?php
namespace app\event;
use app\model\member\Register;
/**
 * 注册成功通知
 */
class MessageRegisterSuccess
{
    /**
     * @param $param
     * @return array|mixed|void
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "REGISTER_SUCCESS") {
            $register_model = new Register();
            $result         = $register_model->registerSuccess($param);
            return $result;
        }
    }
}