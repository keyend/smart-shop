<?php
namespace app\event;
use app\model\order\OrderMessage;
class MessageShopRefundAgree
{
    /**
     * @param $param
     */
    public function handle($param)
    {
        // 发送订单消息
        if ($param["keywords"] == "ORDER_REFUND_AGREE") {
            //发送订单消息
            $model = new OrderMessage();
            return $model->messageOrderRefundAgree($param);
        }
    }
}