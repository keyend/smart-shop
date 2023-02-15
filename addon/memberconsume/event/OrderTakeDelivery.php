<?php
namespace addon\memberconsume\event;
use addon\memberconsume\model\Consume as ConsumeModel;
/**
 * 订单收货事件
 */
class OrderTakeDelivery
{
    public function handle($data)
    {
        $consume_model = new ConsumeModel();
        $res           = $consume_model->memberConsume(['order_id' => $data['order_id'], 'status' => 'receive']);
        return $res;
    }
}