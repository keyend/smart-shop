<?php
namespace addon\fenxiao\event;
use addon\fenxiao\model\FenxiaoOrder;

/**
 * 活动类型
 */
class OrderGoodsRefund
{

    /**
     * 活动类型
     * @return multitype:number unknown
     */
    public function handle($data)
    {
        $order_model = new FenxiaoOrder();
        $res         = $order_model->refund($data['order_goods_id']);
        return $res;
    }
}