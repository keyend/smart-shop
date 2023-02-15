<?php
namespace app\event;
use app\model\order\OrderMessage;
/**
 * 买家收货通知商家
 */
class MessageBuyerReceive
{
    /**
     * @param $param
     * @return array|mixed|void
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "BUYER_TAKE_DELIVERY") {
            $model = new OrderMessage();
            return $model->messageBuyerReceive($param);
        }
    }
}