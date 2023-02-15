<?php
namespace addon\pointexchange\model;
use addon\coupon\model\Coupon;
use app\model\goods\GoodsStock;
use app\model\member\MemberAccount;
use app\model\BaseModel;
use app\model\system\Pay;
use app\model\order\OrderCreate;
use app\model\order\Order as CommonOrder;
use app\model\order\StoreOrder;
use app\model\order\LocalOrder;
use app\model\order\OrderCommon;
use think\facade\Log;
/**
 * 积分兑换订单
 */
class Order extends BaseModel
{
    /**
     * 支付订单
     * @param unknown $out_trade_no
     */
    public function orderPay($data)
    {
        $out_trade_no = $data["out_trade_no"];
        $order_info = model("promotion_exchange_order")->getInfo([['out_trade_no', '=', $out_trade_no]], '*');
        if (empty($order_info)) {
            return $this->error([], "找不到可支付的订单");
        }
        if ($order_info["order_status"] == 1) {
            return $this->error([], "当前订单已支付");
        }
        model("promotion_exchange_order")->startTrans();
        try {
            $order_data = array(
                "order_status" => 1,
                "pay_time" => time()
            );
            switch ($order_info['type']) {
                case 1://商品
                    //添加对应商品订单
                    $order_create = new OrderCreate();
                    $order_no = $order_create->createOrderNo($order_info['site_id']);
                    //订单类型
                    $order_type = $this->orderType($order_info['delivery_type']);
                    $data_order = [
                        'order_no' => $order_no,
                        'site_id' => $order_info['site_id'],
                        'site_name' => '',
                        'order_from' => $order_info['order_from'],
                        'order_from_name' => $order_info['order_from_name'],
                        'order_type' => $order_type['order_type_id'],
                        'order_type_name' => $order_type['order_type_name'],
                        'order_status_name' => $order_type['order_status']['name'],
                        'order_status_action' => json_encode($order_type['order_status'], JSON_UNESCAPED_UNICODE),
                        'out_trade_no' => $out_trade_no,
                        'member_id' => $order_info['member_id'],
                        'name' => $order_info['name'],
                        'mobile' => $order_info['mobile'],
                        'telephone' => $order_info['telephone'],
                        'province_id' => $order_info['province_id'],
                        'city_id' => $order_info['city_id'],
                        'district_id' => $order_info['district_id'],
                        'community_id' => $order_info['community_id'],
                        'address' => $order_info['address'],
                        'full_address' => $order_info['full_address'],
                        'longitude' => $order_info['longitude'],
                        'latitude' => $order_info['latitude'],
                        'buyer_ip' => '',
                        'goods_money' => $order_info['exchange_price']* $order_info['num'],
                        'delivery_money' => $order_info['delivery_price'],
                        'coupon_id' => 0,
                        'coupon_money' => 0,
                        'adjust_money' => 0,
                        'invoice_money' => 0,
                        'promotion_money' => 0,
                        'order_money' => $order_info['price'],
                        'balance_money' => $order_info['balance_money'],
                        'pay_money' => $order_info['pay_money'],
                        'create_time' => time(),
                        'is_enable_refund' => 1,
                        'order_name' => $order_info["exchange_name"],
                        'goods_num' => $order_info['num'],
                        'delivery_type' => $order_info['delivery_type'],
                        'delivery_type_name' => $order_info['delivery_type_name'],
                        'delivery_store_id' => $order_info["delivery_store_id"],
                        "delivery_store_name" => $order_info["delivery_store_name"],
                        "delivery_store_info" => $order_info["delivery_store_info"],
                        "buyer_message" => $order_info["buyer_message"],
                    
                        "invoice_delivery_money" => 0,
                        "taxpayer_number" => '',
                        "invoice_rate" => 0,
                        "invoice_content" => '',
                        "invoice_full_address" => '',
                        "is_invoice" => 0,
                        "invoice_type" => 0,
                        "invoice_title" => '',
                        'is_tax_invoice' => '',
                        'invoice_email' => '',
                        'invoice_title_type' => 0,
                        'buyer_ask_delivery_time' => $order_info['buyer_ask_delivery_time'],//定时达
                        'promotion_type' => 'pointexchange',
                        'promotion_type_name' => '积分兑换'
                    ];
                    $order_id = model("order")->add($data_order);
                    $order_data['relate_order_id'] = $order_id;
                    //查询兑换的商品信息
                    $exchange_info = model("promotion_exchange")->getInfo([['id', '=', $order_info['exchange_id'] ]], 'price,type_id');
                    $sku_info = model("goods_sku")->getInfo([['sku_id', '=', $exchange_info['type_id']]], 'sku_id,sku_name,sku_no,sku_image,is_virtual,goods_class,goods_class_name,cost_price,goods_id,goods_name,sku_spec_format');
                    $data_order_goods = array(
                        'order_id' => $order_id,
                        'site_id' => $order_info['site_id'],
                        'order_no' => $order_no,
                        'member_id' => $order_info['member_id'],
                        'sku_id' => $sku_info['sku_id'],
                        'sku_name' => $sku_info['sku_name'],
                        'sku_image' => $sku_info['sku_image'],
                        'sku_no' => $sku_info['sku_no'],
                        'is_virtual' => $sku_info['is_virtual'],
                        'goods_class' => $sku_info['goods_class'],
                        'goods_class_name' => $sku_info['goods_class_name'],
                        'price' => $order_info['exchange_price'],
                        'cost_price' => $sku_info['cost_price'],
                        'num' => $order_info['num'],
                        'goods_money' => $order_info['exchange_price']* $order_info['num'],
                        'cost_money' => $sku_info['cost_price'] * $order_info['num'],
                        'goods_id' => $sku_info['goods_id'],
                        'delivery_status' => 0,
                        'delivery_status_name' => "未发货",
                        "real_goods_money" => $order_info['exchange_price']* $order_info['num'],
                        'coupon_money' => 0,
                        'promotion_money' => 0,
                    
                        'goods_name' => $sku_info['goods_name'],
                        'sku_spec_format' => $sku_info['sku_spec_format'],
                    );
                    model("order_goods")->add($data_order_goods);
                    $order_common = new OrderCommon();
                    $pay = new Pay();
                    $pay_info = $pay->getPayInfo($out_trade_no);
                    
                    $res = $order_common->orderOnlinePay($pay_info['data']);
                    Log::write($res);
                    break;
                case 2://优惠券
                    $coupon_model = new Coupon();
                    $res = $coupon_model->receiveCoupon($order_info["type_id"], $order_info["site_id"], $order_info["member_id"], 1, 1, 0);
                    break;
                case 3://红包
                    $member_account_model = new MemberAccount();
                    $res = $member_account_model->addMemberAccount($order_info["site_id"], $order_info["member_id"], "balance", $order_info["balance"], "order", "积分兑换,", "积分兑换,获得红包:" . $order_info["balance"]);
                    break;
            }
            $res = model("promotion_exchange_order")->update($order_data, [["order_id", "=", $order_info["order_id"]]]);
            model("promotion_exchange_order")->commit();
            return $this->success();
        } catch (\Exception $e) {
            model("promotion_exchange_order")->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    
    private function orderType($type_name)
    {
        if ($type_name == 'express') {
            $order = new CommonOrder();
            return [
                'order_type_id' => 1,
                'order_type_name' => '普通订单',
                'order_status' => $order->order_status[0]
            ];
        } elseif ($type_name == 'store') {
            $order = new StoreOrder();
            return [
                'order_type_id' => 2,
                'order_type_name' => '自提订单',
                'order_status' => $order->order_status[0]
            ];
        } elseif ($type_name == 'local') {
            $order = new LocalOrder();
            return [
                'order_type_id' => 3,
                'order_type_name' => '外卖订单',
                'order_status' => $order->order_status[0]
            ];
        }
    }
    /**
     * 关闭订单
     * @param unknown $order_id
     */
    public function closeOrder($order_id)
    {
        $order_info = model("promotion_exchange_order")->getInfo([['order_id', '=', $order_id]], '*');
        if (empty($order_info))
            return $this->error();
        model("promotion_exchange_order")->startTrans();
        //循环生成多个订单
        try {
            $data = array(
                "order_status" => -1,
            );
            $result = model("promotion_exchange_order")->update($data, [["order_id", '=', $order_id]]);
            //返还积分
            $member_account_model = new MemberAccount();
            $member_account_result = $member_account_model->addMemberAccount($order_info['site_id'], $order_info["member_id"], "point", $order_info["point"], "refund", "积分兑换", "积分兑换关闭,返还积分:" . $order_info["point"]);
            if ($member_account_result["code"] < 0) {
                model("promotion_exchange_order")->rollback();
                return $member_account_result;
            }
            //判断库存
            $exchange_model = new Exchange();
            switch ($order_info["type"]) {//兑换类型
                case "1"://商品
                    //库存变化
                    $goods_stock_model = new GoodsStock();
                    $stock_result = $goods_stock_model->incStock(["sku_id" => $order_info['type_id'], "num" => $order_info["num"]]);
                    if ($stock_result["code"] != 0) {
                        model("promotion_exchange_order")->rollback();
                        return $stock_result;
                    }
                    break;
                case "2"://优惠券
                    //返回优惠券库存
                    $coupon_model = new Coupon();
                    $result = $coupon_model->incStock([["coupon_type_id", '=', $order_info["type_id"]], ["num", '=', $order_info["num"]]]);
                    break;
                case "3"://红包
                    break;
            }
            //返还套餐库存
            $exchange_result = $exchange_model->incStock(['id' => $order_info['exchange_id'], 'num' => $order_info['num']]);
            if ($exchange_result['code'] < 0) {
                model("promotion_exchange_order")->rollback();
                return $exchange_result;
            }
            if($order_info['type'] == 1 && $order_info['price'] > 0){
                //关闭支付
                $pay_model = new Pay();
                $result = $pay_model->deletePay($order_info['out_trade_no']);//关闭旧支付单据
                if ($result["code"] < 0) {
                    model("promotion_exchange_order")->rollback();
                    return $result;
                }
            }
            model("promotion_exchange_order")->commit();
            return $this->success();
        } catch (\Exception $e) {
            model("promotion_exchange_order")->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 获取积分兑换订单信息
     * @param unknown $id
     */
    public function getOrderInfo($condition, $field = '*')
    {
        $res = model('promotion_exchange_order')->getInfo($condition, $field);
        return $this->success($res);
    }
    /**
     * 获取订单列表
     * @param unknown $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     * @return multitype:string
     */
    public function getOrderList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('promotion_exchange_order')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }
    /**
     * 获取积分兑换订单分页列表
     * @param unknown $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getExchangePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('promotion_exchange_order')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
    /**
     * 发货
     * @param $param
     * @return array
     */
    public function delivery($param)
    {
        $order_id = $param['order_id'];
        $delivery_code = $param['delivery_code'];
        $order_data = array(
            'delivery_status' => 1,
            'delivery_status_name' => '已发货',
            'delivery_code' => $delivery_code
        );
        $res = model("promotion_exchange_order")->update($order_data, [["order_id", "=", $order_id]]);
        return $this->success();
    }
}