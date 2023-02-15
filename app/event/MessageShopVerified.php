<?php
namespace app\event;
use app\model\order\OrderMessage;
class MessageShopVerified
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        // 发送订单消息
        if ($param["keywords"] == "VERIFY") {
            //发送订单消息
            $model = new OrderMessage();
            return $model->messageOrderVerify($param);
        }
    }
}