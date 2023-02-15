<?php
namespace addon\store\model;
use app\model\BaseModel;
use app\model\verify\Verify;
class StoreOrder extends BaseModel
{
    /**
     * 基础支付方式(不考虑实际在线支付方式或者货到付款方式)
     * @var unknown
     */
    public $pay_type = [
        'OFFLINE_PAY' => '现金'
    ];
    /**
     * 获取支付方式
     */
    public function getPayType($site_id)
    {
        //获取订单基础的其他支付方式
        $pay_type = $this->pay_type;
        //获取当前所有在线支付方式
        $onlinepay = event('PayType', ["app_type" => "pc", 'site_id' => $site_id]);
        if (!empty($onlinepay)) {
            foreach ($onlinepay as $k => $v) {
                $pay_type[$v['pay_type']] = $v['pay_type_name'];
            }
        }
        return $pay_type;
    }
    /**
     * 订单支付后操作
     * @param $order
     */
    public function orderPay($order)
    {
        if (empty($order['delivery_store_id'])) {
            return $this->success();
        }
        model('store')->startTrans();
        try {
            model('store')->setInc([['store_id', '=', $order['delivery_store_id']]], 'order_num');
            model('store')->setInc([['store_id', '=', $order['delivery_store_id']]], 'order_money', $order['order_money']);
            model('store_member')->setInc([['member_id', '=', $order['member_id']]], 'order_num');
            model('store_member')->setInc([['member_id', '=', $order['member_id']]], 'order_money', $order['order_money']);
            //如果是否门店收银订单,订单会自动核销
            if ($order['promotion_type'] == 'store') {
                //主动调用核销流程
                $verifier_info = array(
                    'verifier_id'   => 0,
                    'verifier_name' => '收银台订单自动核销',
                );
                $verify_model  = new Verify();
                $result        = $verify_model->verify($verifier_info, $order['delivery_code']);
                if ($result['code'] < 0) {
                    model('store')->rollback();
                    return $result;
                }
            }
            model('store')->commit();
            return $this->success();
        } catch (\Exception $e) {
            model('store')->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 门店订单完成后操作
     * @param $order
     */
    public function orderComplete($order_id)
    {
        $order_info = model('order')->getInfo([['order_id', '=', $order_id]], '*');
        if (empty($order_info['delivery_store_id'])) {
            return $this->success();
        }
        model('store')->setInc([['store_id', '=', $order_info['delivery_store_id']]], 'order_complete_num');
        model('store')->setInc([['store_id', '=', $order_info['delivery_store_id']]], 'order_complete_money', $order_info['order_money']);
        model('store_member')->setInc([['member_id', '=', $order_info['member_id']]], 'order_complete_num');
        model('store_member')->setInc([['member_id', '=', $order_info['member_id']]], 'order_complete_money', $order_info['order_money']);
        return $this->success();
    }
}