<?php
namespace app\event;
use app\model\order\OrderMessage;
/**
 * 订单关闭发送消息
 */
class MessageOrderClose
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "ORDER_CLOSE") {
            //发送订单消息
            $model = new OrderMessage();
            return $model->messageOrderClose($param);
        }
    }
}