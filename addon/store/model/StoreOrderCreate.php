<?php
namespace addon\store\model;
use addon\coupon\model\Coupon;
use app\model\express\Config as ExpressConfig;
use app\model\express\Express;
use app\model\goods\Goods;
use app\model\goods\GoodsStock;
use app\model\member\Member;
use app\model\member\MemberAccount;
use app\model\order\OrderCreate;
use app\model\store\Store;
use app\model\system\Pay;
use think\facade\Cache;
use app\model\order\Config;
use app\model\express\Local;
/**
 * 订单创建(秒杀)
 *
 * @author Administrator
 *
 */
class StoreOrderCreate extends OrderCreate
{
    private $goods_money = 0;//商品金额
    private $balance_money = 0;//余额
    private $delivery_money = 0;//配送费用
    private $coupon_money = 0;//优惠券金额
    private $adjust_money = 0;//调整金额
    private $invoice_money = 0;//发票费用
    private $promotion_money = 0;//优惠金额
    private $order_money = 0;//订单金额
    private $pay_money = 0;//支付总价
    private $is_virtual = 0;  //是否是虚拟类订单
    private $order_name = '';  //订单详情
    private $goods_num = 0;  //商品种数
    private $member_balance_money = 0;//会员账户余额(计算过程中会逐次减少)
    private $pay_type = 'ONLINE_PAY';//支付方式
    private $invoice_delivery_money = 0;
    private $error = 0;  //是否有错误
    private $error_msg = '';  //错误描述
    /**
     * 订单创建
     * @param unknown $data
     */
    public function create($data)
    {
        //查询出会员相关信息
        $calculate_data = $this->calculate($data);
        if (isset($calculate_data['code']) && $calculate_data['code'] < 0)
            return $calculate_data;
        if ($this->error > 0) {
            return $this->error(['error_code' => $this->error], $this->error_msg);
        }
        $pay          = new Pay();
        $out_trade_no = $pay->createOutTradeNo();
        model("order")->startTrans();
        //循环生成多个订单
        try {
            $pay_money         = 0;
            $goods_stock_model = new GoodsStock();
            $shop_goods_list = $calculate_data['shop_goods_list'];
            $item_delivery      = $shop_goods_list['delivery'] ?? [];
            $delivery_type      = $item_delivery['delivery_type'] ?? '';
            $delivery_type_name = Express::express_type[$delivery_type]["title"] ?? '';
            //订单主表
            $order_type = $this->orderType($shop_goods_list, $calculate_data);
            $order_no   = $this->createOrderNo($shop_goods_list['site_id']);
            $data_order = [
                'order_no'              => $order_no,
                'site_id'               => $shop_goods_list['site_id'],
                'order_from'            => $data['order_from'],
                'order_from_name'       => $data['order_from_name'],
                'order_type'            => $order_type['order_type_id'],
                'order_type_name'       => $order_type['order_type_name'],
                'order_status_name'     => $order_type['order_status']['name'],
                'order_status_action'   => json_encode($order_type['order_status'], JSON_UNESCAPED_UNICODE),
                'out_trade_no'          => $out_trade_no,
                'member_id'             => $data['member_id'],
                'name'                  => $calculate_data['member_address']['name'] ?? '',
                'mobile'                => $calculate_data['member_address']['mobile'] ?? '',
                'telephone'             => $calculate_data['member_address']['telephone'] ?? '',
                'province_id'           => $calculate_data['member_address']['province_id'] ?? '',
                'city_id'               => $calculate_data['member_address']['city_id'] ?? '',
                'district_id'           => $calculate_data['member_address']['district_id'] ?? '',
                'community_id'          => $calculate_data['member_address']['community_id'] ?? '',
                'address'               => $calculate_data['member_address']['address'] ?? '',
                'full_address'          => $calculate_data['member_address']['full_address'] ?? '',
                'longitude'             => $calculate_data['member_address']['longitude'] ?? '',
                'latitude'              => $calculate_data['member_address']['latitude'] ?? '',
                'buyer_ip'              => request()->ip(),
                'goods_money'           => $shop_goods_list['goods_money'],
                'delivery_money'        => $shop_goods_list['delivery_money'],
                'coupon_id'             => isset($shop_goods_list['coupon_id']) ? $shop_goods_list['coupon_id'] : 0,
                'coupon_money'          => $shop_goods_list['coupon_money'] ?? 0,
                'adjust_money'          => $shop_goods_list['adjust_money'],
                'invoice_money'         => $shop_goods_list['invoice_money'],
                'promotion_money'       => $shop_goods_list['promotion_money'],
                'order_money'           => $shop_goods_list['order_money'],
                'balance_money'         => $shop_goods_list['balance_money'],
                'pay_money'             => $shop_goods_list['pay_money'],
                'create_time'           => time(),
                'is_enable_refund'      => 0,
                'order_name'            => $shop_goods_list["order_name"],
                'goods_num'             => $shop_goods_list['goods_num'],
                'delivery_type'         => $delivery_type,
                'delivery_type_name'    => $delivery_type_name,
                'delivery_store_id'     => $calculate_data["delivery_store_id"] ?? 0,
                "delivery_store_name"   => $calculate_data["delivery_store_name"] ?? '',
                "delivery_store_info"   => $calculate_data["delivery_store_info"] ?? '',
                "buyer_message"         => $data["buyer_message"],
                "promotion_type"        => "store",
                "promotion_type_name"   => "门店收银",
                "promotion_status_name" => "",
                "invoice_delivery_money" => $shop_goods_list["invoice_delivery_money"] ?? 0,
                "taxpayer_number"        => $shop_goods_list["taxpayer_number"] ?? '',
                "invoice_rate"           => $shop_goods_list["invoice_rate"] ?? 0,
                "invoice_content"        => $shop_goods_list["invoice_content"] ?? '',
                "invoice_full_address"   => $shop_goods_list["invoice_full_address"] ?? '',
                "is_invoice"             => $shop_goods_list["is_invoice"] ?? 0,
                "invoice_type"           => $shop_goods_list["invoice_type"] ?? 0,
                "invoice_title"          => $shop_goods_list["invoice_title"] ?? '',
                'is_tax_invoice'         => $shop_goods_list["is_tax_invoice"] ?? '',
                'invoice_email'          => $shop_goods_list["invoice_email"] ?? '',
                'invoice_title_type'     => $shop_goods_list["invoice_email"] ?? 0,
            ];
            $order_id  = model("order")->add($data_order);
            $pay_money += $shop_goods_list['pay_money'];
            //订单项目表
            foreach ($shop_goods_list['goods_list'] as $k_order_goods => $order_goods) {
                $data_order_goods = array(
                    'order_id'             => $order_id,
                    'site_id'              => $shop_goods_list['site_id'],
                    'order_no'             => $order_no,
                    'member_id'            => $data['member_id'],
                    'sku_id'               => $order_goods['sku_id'],
                    'sku_name'             => $order_goods['sku_name'],
                    'sku_image'            => $order_goods['sku_image'],
                    'sku_no'               => $order_goods['sku_no'],
                    'is_virtual'           => $order_goods['is_virtual'],
                    'goods_class'          => $order_goods['goods_class'],
                    'goods_class_name'     => $order_goods['goods_class_name'],
                    'price'                => $order_goods['discount_price'],
                    'cost_price'           => $order_goods['cost_price'],
                    'num'                  => $order_goods['num'],
                    'goods_money'          => $order_goods['discount_price'] * $order_goods['num'],
                    'cost_money'           => $order_goods['cost_price'] * $order_goods['num'],
                    'goods_id'             => $order_goods['goods_id'],
                    'delivery_status'      => 0,
                    'delivery_status_name' => "未发货",
                    "real_goods_money"     => $order_goods["real_goods_money"],
                    'coupon_money'         => $order_goods["coupon_money"] ?? 0,
                    'promotion_money'      => $order_goods["promotion_money"],
                    'goods_name'      => $order_goods['goods_name'],
                    'sku_spec_format' => $order_goods['sku_spec_format'],
                );
                model("order_goods")->add($data_order_goods);
                //库存变化
                $stock_result = $goods_stock_model->decStock(["sku_id" => $order_goods['sku_id'], "num" => $order_goods['num']]);
                if ($stock_result["code"] != 0) {
                    model("order")->rollback();
                    return $stock_result;
                }
            }
            //扣除余额(统一扣除)
            if ($calculate_data["balance_money"] > 0) {
                $balance_result = $this->storeUseBalance($calculate_data);
                if ($balance_result["code"] < 0) {
                    model("order")->rollback();
                    return $balance_result;
                }
            }
            //优惠券
            if ($data_order['coupon_id'] > 0 && $data_order['coupon_money'] > 0) {
                //优惠券处理方案
                $member_coupon_model = new Coupon();
                $coupon_use_result   = $member_coupon_model->useCoupon($data_order['coupon_id'], $data['member_id'], $order_id);//使用优惠券
                if ($coupon_use_result['code'] < 0) {
                    model("order")->rollback();
                    return $this->error('', "COUPON_ERROR");
                }
            }
            $result_list = event("OrderCreate", ['order_id' => $order_id]);
            if (!empty($result_list)) {
                foreach ($result_list as $k => $v) {
                    if (!empty($v) && $v["code"] < 0) {
                        model("order")->rollback();
                        return $v;
                    }
                }
            }
            //生成整体支付单据
            $pay->addPay($data['site_id'], $out_trade_no, $this->pay_type, $this->order_name, $this->order_name, $this->pay_money, '', 'OrderPayNotify', '');
            $this->addOrderCronClose($order_id, $data['site_id']);//增加关闭订单自动事件
            Cache::tag("order_create_cash_" . $data['member_id'])->clear();
            model("order")->commit();
            return $this->success($out_trade_no);
        } catch (\Exception $e) {
            model("order")->rollback();
            return $this->error('', $e->getMessage());
        }
    }
    /**
     * 订单计算
     * @param unknown $data
     */
    public function calculate($data)
    {
        $data = $this->storeInfo($data);
        $data = $this->initMemberAccount($data);//初始化会员账户
        //余额付款
        if ($data['is_balance'] > 0) {
            $this->member_balance_money = $data["member_account"]["balance_total"] ?? 0;
        }
        //商品列表信息
        $shop_goods_list         = $this->getOrderGoodsCalculate($data);
        $data['shop_goods_list'] = $this->shopOrderCalculate($shop_goods_list, $data);
        //总结计算
        $data['delivery_money']         = $this->delivery_money;
        $data['coupon_money']           = $this->coupon_money;
        $data['adjust_money']           = $this->adjust_money;
        $data['invoice_money']          = $this->invoice_money;
        $data['invoice_delivery_money'] = $this->invoice_delivery_money;
        $data['promotion_money']        = $this->promotion_money;
        $data['order_money']            = $this->order_money;
        $data['balance_money']          = $this->balance_money;
        $data['pay_money']              = $this->pay_money;
        $data['goods_money']            = $this->goods_money;
        $data['goods_num']              = $this->goods_num;
        $data['is_virtual']             = $this->is_virtual;
        return $data;
    }
    /**
     * 待付款订单
     * @param unknown $data
     */
    public function orderPayment($data)
    {
        $calculate_data = $this->calculate($data);
        if (isset($calculate_data['code']) && $calculate_data['code'] < 0)
            return $calculate_data;
        //1、查询会员优惠券
        $coupon_list                                      = $this->getOrderCouponList($calculate_data);
        $calculate_data['shop_goods_list']["coupon_list"] = $coupon_list;
        $express_type                                      = [];
        $calculate_data['shop_goods_list']["express_type"] = $express_type;
        return $this->success($calculate_data);
    }
    /**
     * 获取商品的计算信息
     * @param unknown $data
     */
    public function getOrderGoodsCalculate($data)
    {
        $shop_goods_list = [];
        //通过秒杀id查询 商品数据
//        $cache = Cache::get("order_create_store_".$data['id'].'_'.$data['num'].'_'.$data['member_id']);
//        if(!empty($cache))
//        {
//            return $cache;
//        }
        $goods_list                    = $this->getStoreGoodsList($data["sku_ids"], $data['nums'], $data['member_id'], $data['site_id'], $data['store_id']);
        $goods_list['promotion_money'] = 0;
        $shop_goods_list               = $goods_list;
        //满减优惠
        $shop_goods_list = $this->manjianPromotion($shop_goods_list);
//        Cache::tag("order_create_store_".$data['member_id'])->set("order_create_seckill_".$data['id'].'_'.$data['num'].'_'.$data['member_id'], $shop_goods_list, 600);
        return $shop_goods_list;
    }
    /**
     * 获取秒杀商品列表信息
     * @param unknown $bl_id
     */
    public function getStoreGoodsList($sku_ids, $nums, $member_id, $site_id, $store_id)
    {
        $goods_model = new Goods();
        //组装商品列表
        $field      = 'sgs.store_stock,sgs.store_sale_num,ngs.sku_name, ngs.sku_id,ngs.sku_no,
            ngs.price, ngs.discount_price, ngs.cost_price, ngs.stock, ngs.weight, ngs.volume, ngs.sku_image, 
            ngs.site_id,   ngs.goods_state, ngs.is_virtual, 
            ngs.is_free_shipping, ngs.shipping_template, ngs.goods_class, ngs.goods_class_name, ngs.goods_id,ngs.sku_spec_format,ngs.goods_name';
        $alias      = 'sgs';
        $join       = [
            [
                'goods_sku ngs',
                'sgs.sku_id = ngs.sku_id',
                'inner'
            ],
        ];
        $goods_list = model("store_goods_sku")->getList([['sgs.sku_id', 'in', $sku_ids], ['ngs.site_id', '=', $site_id], ['sgs.store_id', '=', $store_id]], $field, '', $alias, $join);
//            $shop_goods_list = [];
        if (!empty($goods_list)) {
            foreach ($goods_list as $k => $v) {
                $num      = $nums[$v["sku_id"]] ?? 0;
                $v["num"] = $num;
                $site_id  = $v['site_id'];
                $price_result          = $goods_model->getGoodsPrice($v['sku_id'], $member_id);
                $price_info            = $price_result["data"];
                $price                 = $price_info["price"];
                $v['price']            = $price;
                $v['goods_money']      = $price * $v['num'];
                $v['real_goods_money'] = $v['goods_money'];
                $v['coupon_money']     = 0;//优惠券金额
                $v['promotion_money']  = 0;//优惠金额
                if (isset($shop_goods_list)) {
                    $shop_goods_list['goods_list'][]   = $v;
                    $shop_goods_list['order_name']     = string_split($shop_goods_list['order_name'], ",", $v['sku_name']);
                    $shop_goods_list['goods_num']      += $num;
                    $shop_goods_list['goods_money']    += $v['goods_money'];
                    $shop_goods_list['goods_list_str'] = $shop_goods_list['goods_list_str'] . ';' . $v['sku_id'] . ':' . $num;
                } else {
                    $shop_goods_list['site_id']        = $site_id;
                    $shop_goods_list['goods_money']    = $v['goods_money'];
                    $shop_goods_list['goods_list_str'] = $v['sku_id'] . ':' . $num;
                    $shop_goods_list['order_name']     = string_split("", ",", $v['sku_name']);
                    $shop_goods_list['goods_num']      = $num;
                    $shop_goods_list['goods_list'][]   = $v;
                }
            }
        }
        return $shop_goods_list;
    }
    /**
     * 获取店铺订单计算
     * @param unknown $site_id 店铺id
     * @param unknown $goods_money 商品总价
     * @param unknown $goods_list 店铺商品列表
     * @param unknown $data 传输生成订单数据
     */
    public function shopOrderCalculate($shop_goods, $data)
    {
        $site_id = $shop_goods['site_id'];
        //交易配置
        $config_model        = new Config();
        $order_config_result = $config_model->getOrderEventTimeConfig($site_id);
        $order_config        = $order_config_result["data"];
        $shop_goods['order_config'] = $order_config['value'] ?? [];
        //定义计算金额
        $goods_money     = $shop_goods['goods_money'];  //商品金额
        $delivery_money  = 0;  //配送费用
        $promotion_money = $shop_goods['promotion_money'];  //优惠费用（满减）
        $coupon_money    = 0;     //优惠券费用
        $adjust_money    = 0;     //调整金额
        $invoice_money   = 0;    //发票金额
        $order_money     = 0;      //订单金额
        $balance_money   = 0;    //会员余额
        $pay_money       = 0;        //应付金额
        //计算邮费
        $express_config_model = new ExpressConfig();
        //查询店铺是否开启门店自提
        $store_config_result                     = $express_config_model->getStoreConfig($site_id);
        $store_config                            = $store_config_result["data"];
        $shop_goods["store_config"]              = $store_config;
        $shop_goods['delivery']["delivery_type"] = "store";
        //门店自提
        $delivery_money = 0;
        if (empty($data["store_id"])) {
            $this->error     = 1;
            $this->error_msg = "门店未选择!";
        }
        $shop_goods['delivery']['store_id'] = $data["store_id"];
        //发票相关
        $shop_goods = $this->invoice($shop_goods, $data);
        $order_money               = $goods_money + $delivery_money - $promotion_money - $shop_goods['invoice_money'] + $shop_goods['invoice_delivery_money'];
        $shop_goods['order_money'] = $order_money;//订单总金额
        //优惠券活动(采用站点id:coupon_id)
        $shop_goods   = $this->couponPromotion($shop_goods, $data);
        $coupon_money = $shop_goods['coupon_money'] ?? 0;
        $order_money  = $shop_goods['order_money'];
        //理论上是多余的操作
        if ($order_money < 0) {
            $order_money = 0;
        }
        //余额抵扣(判断是否使用余额)
        if ($this->member_balance_money > 0) {
            if ($order_money <= $this->member_balance_money) {
                $balance_money = $order_money;
            } else {
                $balance_money = $this->member_balance_money;
            }
        } else {
            $balance_money = 0;
        }
        $pay_money                  = $order_money - $balance_money;//计算出实际支付金额
        $this->member_balance_money -= $balance_money;//预减少账户余额
        $this->balance_money        += $balance_money;//累计余额
        //总结计算
        $shop_goods['goods_money']     = $goods_money;
        $shop_goods['delivery_money']  = $delivery_money;
        $shop_goods['adjust_money']    = $adjust_money;
        $shop_goods['promotion_money'] = $promotion_money;
        $shop_goods['order_money']     = $order_money;
        $shop_goods['balance_money']   = $balance_money;
        $shop_goods['pay_money']       = $pay_money;
        $this->goods_money            += $goods_money;
        $this->delivery_money         += $delivery_money;
        $this->coupon_money           += $coupon_money;
        $this->adjust_money           += $adjust_money;
        $this->invoice_money          += $shop_goods['invoice_money'];
        $this->invoice_delivery_money += $shop_goods['invoice_delivery_money'];
        $this->promotion_money        += $promotion_money;
        $this->order_money            += $order_money;
        $this->pay_money              += $pay_money;
        $this->goods_num  += $shop_goods["goods_num"];
        $this->order_name = string_split($this->order_name, ",", $shop_goods["order_name"]);
        return $shop_goods;
    }
    /**
     * 补齐门店数据
     * @param $data
     */
    public function storeInfo($data)
    {
        $delivery_store_id = $data["store_id"] ?? 0;//门店id
        if ($delivery_store_id > 0) {
            $store_model       = new Store();
            $condition         = array(
                ["store_id", "=", $delivery_store_id],
            );
            $store_info_result = $store_model->getStoreInfo($condition);
            $store_info        = $store_info_result["data"] ?? [];
            if (empty($store_info)) {
                $this->error     = 1;
                $this->error_msg = "当前门店不存在或未开启!";
            } else {
                $data['site_id']             = $store_info['site_id'];
                $data["delivery_store_id"]   = $delivery_store_id;
                $delivery_store_name         = $store_info_result["data"]["store_name"];
                $data["delivery_store_name"] = $delivery_store_name;
                $delivery_store_info         = array(
                    "open_date"    => $store_info["open_date"],
                    "full_address" => $store_info["full_address"] . $store_info["address"],
                    "longitude"    => $store_info["longitude"],
                    "latitude"     => $store_info["latitude"],
                    "telphone"     => $store_info["telphone"],
                    "store_image"  => $store_info["store_image"],
                );
                $data["delivery_store_info"] = json_encode($delivery_store_info, JSON_UNESCAPED_UNICODE);
                $data["member_address"] = array(
                    'name'         => '门店收银',
                    'mobile'       => '',
                    'telephone'    => '',
                    'province_id'  => $store_info['province_id'],
                    'city_id'      => $store_info['city_id'],
                    'district_id'  => $store_info['district_id'],
                    'community_id' => $store_info['community_id'],
                    'address'      => $store_info['address'],
                    'full_address' => str_replace(",", "-", $store_info['full_address']),
                    'longitude'    => $store_info['longitude'],
                    'latitude'     => $store_info['latitude'],
                );
            }
        } else {
            $this->error     = 1;
            $this->error_msg = "配送门店不可为空!";
        }
        return $data;
    }
    /**
     * 使用余额
     * @param $order_data
     * @return array
     */
    public function storeUseBalance($data)
    {
        $this->pay_type       = "BALANCE";
        $balance_money        = $data["member_account"]["balance_money"];//不可提现余额
        $balance              = $data["member_account"]["balance"];//可提现余额
        $member_account_model = new MemberAccount();
        $surplus_banance      = $data["balance_money"];
        //优先扣除不可提现余额
        if ($balance > 0) {
            if ($balance >= $surplus_banance) {
                $real_balance = $surplus_banance;
            } else {
                $real_balance = $balance;
            }
            $result          = $member_account_model->addMemberAccount($data['site_id'], $data["member_id"], "balance", -$real_balance, "order", "余额抵扣", "订单余额抵扣,扣除不可提现余额:" . $real_balance);
            $surplus_banance -= $real_balance;
        }
        if ($surplus_banance > 0) {
            $result = $member_account_model->addMemberAccount($data['site_id'], $data["member_id"], "balance_money", -$surplus_banance, "order", "余额抵扣", "订单余额抵扣,扣除可提现余额:" . $surplus_banance);
        }
        return $result;
    }
}