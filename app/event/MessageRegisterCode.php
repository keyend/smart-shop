<?php
namespace app\event;
use app\model\member\Register;
/**
 * 注册发送验证码
 */
class MessageRegisterCode
{
    /**
     * @param $param
     * @return array|mixed|void
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "REGISTER_CODE") {
            $register_model = new Register();
            $result         = $register_model->registerCode($param);
            return $result;
        }
    }
}