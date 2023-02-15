<?php
namespace app\event;
use app\model\order\OrderMessage;
/**
 * 订单收货发送消息
 */
class MessageOrderReceive
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        // 发送订单消息
        if ($param["keywords"] == "ORDER_TAKE_DELIVERY") {
            //发送订单消息
            $model = new OrderMessage();
            return $model->messageOrderTakeDelivery($param);
        }
    }
}