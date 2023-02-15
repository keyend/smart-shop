<?php
namespace addon\fenxiao\event;
use addon\fenxiao\model\FenxiaoOrder;

/**
 * 活动类型
 */
class OrderSettlement
{

    /**
     * 活动类型
     * @return multitype:number unknown
     */
    public function handle($data)
    {
        $fenxiao_order_model = new FenxiaoOrder();
        $fenxiao_order_model->settlement($data['order_id']);
        $res = $fenxiao_order_model->calculateOrder($data['order_id']);
        return $res;
    }
}