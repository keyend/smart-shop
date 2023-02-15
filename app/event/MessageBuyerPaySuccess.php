<?php
namespace app\event;
use app\model\order\OrderMessage;
/**
 * 买家支付成功通知商家
 */
class MessageBuyerPaySuccess
{
    /**
     * @param $param
     * @return array|mixed|void
     */
    public function handle($param)
    {
        //发送订单消息
        if ($param["keywords"] == "BUYER_PAY") {
            $model = new OrderMessage();
            return $model->messageBuyerPaySuccess($param);
        }
    }
}