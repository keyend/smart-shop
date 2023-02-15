<?php
namespace app\event;
use app\model\system\Stat;
/**
 * 订单支付后店铺点单计算
 */
class ShopOrderCalc
{
    /**
     * 传入订单信息
     * @param unknown $data
     */
    public function handle($data)
    {
        //添加统计
        $stat = new Stat();
        $data = [
            'site_id'         => $data['site_id'],
            'order_total'     => $data['order_money'],
            'shipping_total'  => $data['delivery_money'],
            'order_pay_count' => 1,
            'goods_pay_count' => $data['goods_num'],
        ];
        $res  = $stat->addShopStat($data);
        return $res;
    }
}