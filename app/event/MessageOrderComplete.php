<?php
namespace app\event;
use app\model\order\OrderMessage;
/**
 * 订单关闭发送消息
 */
class MessageOrderComplete
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        // 发送订单消息
        if ($param["keywords"] == "ORDER_COMPLETE") {
            //发送订单消息
            $model = new OrderMessage();
            return $model->messageOrderComplete($param);
        }
    }
}