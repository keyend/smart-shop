<?php
namespace app\shop\controller;
use app\model\order\OrderCommon as OrderCommonModel;
/**
 * 自提订单
 * Class storeorder
 * @package app\shop\controller
 */
class Storeorder extends BaseShop
{
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }
    /**
     * 订单详情
     * @return mixed
     */
    public function detail()
    {
        $order_id            = input("order_id", 0);
        $order_common_model  = new OrderCommonModel();
        $order_detail_result = $order_common_model->getOrderDetail($order_id);
        $order_detail        = $order_detail_result["data"];
        $this->assign("order_detail", $order_detail);
        $this->assign("http_type", get_http_type());
        return $this->fetch("storeorder/detail");
    }
    /**
     * 订单关闭
     * @return mixed
     */
    public function close()
    {
    }
    /**
     * 订单调价
     * @return mixed
     */
    public function adjustprice()
    {
    }
}