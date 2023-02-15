<?php
namespace app\event;
use app\model\order\OrderMessage;
/**
 * 订单发货发送消息
 */
class MessageOrderDelivery
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        // 发送订单消息
        if ($param["keywords"] == "ORDER_DELIVERY") {
            //发送订单消息
            $model = new OrderMessage();
            return $model->messageOrderDelivery($param);
        }
    }
}