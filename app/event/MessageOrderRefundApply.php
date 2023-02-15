<?php
namespace app\event;
use app\model\order\OrderMessage;
class MessageOrderRefundApply
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        // 发送订单消息
        if ($param["keywords"] == "BUYER_REFUND") {
            //发送订单消息
            $model = new OrderMessage();
            return $model->messageOrderRefundApply($param);
        }
    }
}