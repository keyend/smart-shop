<?php
namespace app\event;
use app\model\order\OrderMessage;
/**
 * 订单创建发送消息
 */
class MessageOrderCreate
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "ORDER_CREATE") {
            //发送订单消息
            $model = new OrderMessage();
            return $model->messageOrderCreate($param);
        }
    }
}