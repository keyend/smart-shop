<?php
namespace app\event;
use app\model\order\OrderMessage;
/**
 * 订单创建发送消息
 */
class MessageOrderPaySuccess
{
    /**
     * @param $param
     * @return array|mixed|void
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "ORDER_PAY") {
            $model = new OrderMessage();
            $model->messagePaySuccess($param);
        }
    }
}