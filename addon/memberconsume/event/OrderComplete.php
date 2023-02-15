<?php
namespace addon\memberconsume\event;
use addon\memberconsume\model\Consume as ConsumeModel;
/**
 * 订单完成事件
 */
class OrderComplete
{
    /**
     * @param $param
     * @return array|\multitype
     */
    public function handle($data)
    {
        $consume_model = new ConsumeModel();
        $res           = $consume_model->memberConsume(['order_id' => $data['order_id'], 'status' => 'complete']);
        return $res;
    }
}