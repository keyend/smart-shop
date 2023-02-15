<?php
namespace app\event;
use app\model\order\OrderMessage;
class MessageShopRefundRefuse
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        // 发送订单消息
        if ($param["keywords"] == "ORDER_REFUND_REFUSE") {
            //发送订单消息
            $model = new OrderMessage();
            return $model->messageOrderRefundRefuse($param);
        }
    }
}