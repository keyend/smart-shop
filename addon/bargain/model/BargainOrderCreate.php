<?php
namespace addon\bargain\model;
use app\model\order\OrderCreate;
use app\model\goods\GoodsStock;
use app\model\store\Store;
use think\facade\Cache;
use app\model\express\Express;
use app\model\system\Pay;
use app\model\express\Config as ExpressConfig;
use app\model\order\Config;
use app\model\express\Local;
/**
 * 订单创建（砍价）
 *
 * @author Administrator
 *
 */
class BargainOrderCreate extends OrderCreate
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
                'site_name'             => $shop_goods_list['site_name'],
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
                'delivery_store_id'     => $shop_goods_list["delivery_store_id"] ?? 0,
                "delivery_store_name"   => $shop_goods_list["delivery_store_name"] ?? '',
                "delivery_store_info"   => $shop_goods_list["delivery_store_info"] ?? '',
                "buyer_message"         => $data["buyer_message"],
                "promotion_type"        => "bargain",
                "promotion_type_name"   => "砍价",
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
                'invoice_title_type'     => $shop_goods_list["invoice_title_type"] ?? 0,
                'buyer_ask_delivery_time' => $shop_goods_list['buyer_ask_delivery_time'] ?? '',
                'is_fenxiao'              => $calculate_data['bargain_info']['is_fenxiao']
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
                    'price'                => $order_goods['price'],
                    'cost_price'           => $order_goods['cost_price'],
                    'num'                  => $order_goods['num'],
                    'goods_money'          => $order_goods['goods_money'],
                    'cost_money'           => $order_goods['cost_price'] * $order_goods['num'],
                    'goods_id'             => $order_goods['goods_id'],
                    'delivery_status'      => 0,
                    'delivery_status_name' => "未发货",
                    "real_goods_money"     => $order_goods["real_goods_money"],
                    'coupon_money'         => $order_goods["coupon_money"] ?? 0,
                    'promotion_money'      => $order_goods["promotion_money"],
                    'goods_name'      => $order_goods['goods_name'],
                    'sku_spec_format' => $order_goods['sku_spec_format'],
                    'is_fenxiao'      => $calculate_data['bargain_info']['is_fenxiao']
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
                $this->pay_type = "BALANCE";
                $balance_result = $this->useBalance($calculate_data, $data['site_id']);
                if ($balance_result["code"] < 0) {
                    model("order")->rollback();
                    return $balance_result;
                }
            }
            // 砍价绑定订单id
            $bargain_data = ['order_id' => $order_id];
            if ($calculate_data['bargain_info']['buy_type'] == 0) {
                $bargain_data['status'] = 1;
            }
            model('promotion_bargain_launch')->update($bargain_data, [['launch_id', '=', $data['id']]]);
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
            $pay->addPay($shop_goods_list['site_id'], $out_trade_no, $this->pay_type, $this->order_name, $this->order_name, $this->pay_money, '', 'OrderPayNotify', '');
            $this->addOrderCronClose($order_id, $shop_goods_list['site_id']);//增加关闭订单自动事件
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
        $data = $this->initMemberAddress($data);
        $data = $this->initMemberAccount($data);//初始化会员账户
        //余额付款
        if ($data['is_balance'] > 0) {
            $this->member_balance_money = $data["member_account"]["balance_total"] ?? 0;
        }
        //查询砍价信息
        $bargain_model       = new Bargain();
        $launch_id           = $data["id"];
        $bargain_info_result = $bargain_model->getBargainLaunchDetail([["launch_id", "=", $launch_id], ['site_id', '=', $data['site_id']]]);
        $bargain_info        = $bargain_info_result["data"];
        $data["bargain_info"] = $bargain_info;
        //商品列表信息
        $shop_goods_list = $this->getOrderGoodsCalculate($data);
        //判断砍价是否成功
        if ($bargain_info["buy_type"] == 1 && $bargain_info["status"] != 1) {
            $this->error     = 1;
            $this->error_msg = "该商品您尚未砍价成功!";
        }
        //判断砍价是否已经下单了
        if (!empty($bargain_info["order_id"])) {
            $this->error     = 1;
            $this->error_msg = "本次砍价您已下单过了!";
        }
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
        $calculate_data  = $this->calculate($data);
        $shop_goods_list = $calculate_data['shop_goods_list'];
        $calculate_data['shop_goods_list']["coupon_list"] = [];
        $express_type                                     = [];
        if ($this->is_virtual == 0) {
            //2. 查询店铺配送方式（1. 物流  2. 自提  3. 外卖）
            if ($calculate_data['shop_goods_list']["express_config"]["is_use"] == 1) {
                $express_type[] = ["title" => Express::express_type["express"]["title"], "name" => "express"];
            }
            //查询店铺是否开启门店自提
            if ($calculate_data['shop_goods_list']["store_config"]["is_use"] == 1) {
                //根据坐标查询门店
                $store_model     = new Store();
                $store_condition = array(
                    ['site_id', '=', $data['site_id']],
                    ['is_pickup', '=', 1],
                    ['status', '=', 1],
                    ['is_frozen', '=', 0],
                );
                $latlng            = array(
                    'lat' => $data['latitude'],
                    'lng' => $data['longitude'],
                );
                $store_list_result = $store_model->getLocationStoreList($store_condition, '*', $latlng);
                $store_list        = $store_list_result["data"];
                //如果用户默认选中了门店
                $store_id = 0;
                if ($data['default_store_id'] > 0) {
                    if (!empty($store_list)) {
                        $store_array = array_column($store_list, 'store_id');
                        if (in_array($data['default_store_id'], $store_array)) {
                            $store_id = $data['default_store_id'];
                        } else {
                            $store_condition   = array(
                                ['site_id', '=', $data['site_id']],
                                ['is_pickup', '=', 1],
                                ['status', '=', 1],
                                ['is_frozen', '=', 0],
                                ['store_id', '=', $data['default_store_id']],
                            );
                            $store_info_result = $store_model->getStoreInfo($store_condition, '*');
                            $store_info        = $store_info_result['data'];
                            if (!empty($store_info)) {
                                $store_id     = $data['default_store_id'];
                                $store_list[] = $store_info;
                            }
                        }
                    }
                }
                $express_type[] = ["title" => Express::express_type["store"]["title"], "name" => "store", "store_list" => $store_list, 'store_id' => $store_id];
            }
            //查询店铺是否开启外卖配送
            if ($calculate_data['shop_goods_list']["local_config"]["is_use"] == 1) {
                //查询本店的通讯地址
                $express_type[] = ["title" => "外卖配送", "name" => "local"];
            }
        }
        $calculate_data['shop_goods_list']["express_type"] = $express_type;
        return $this->success($calculate_data);
    }
    /**
     * 获取商品的计算信息
     * @param unknown $data
     */
    public function getOrderGoodsCalculate($data)
    {
        $goods_list                    = $this->getBargainGoodsInfo($data);
        $goods_list['promotion_money'] = 0;
        $shop_goods_list               = $goods_list;
        return $shop_goods_list;
    }
    /**
     * 获取团购商品列表信息
     * @param unknown $bl_id
     */
    public function getBargainGoodsInfo($data)
    {
        //组装商品列表
        $field = 'sku_id,sku_name, sku_no,
            price, discount_price, cost_price, stock, weight, volume, sku_image, 
            ngs.site_id, goods_state, is_virtual, 
            is_free_shipping, shipping_template, goods_class, goods_class_name,goods_id, ns.site_name,ngs.sku_spec_format,ngs.goods_name';
        $join = [
            [
                'site ns',
                'ngs.site_id = ns.site_id',
                'inner'
            ]
        ];
        $info            = model("goods_sku")->getInfo([['ngs.sku_id', '=', $data["bargain_info"]["sku_id"]], ['ngs.site_id', '=', $data['site_id']]], $field, 'ngs', $join);
        $shop_goods_list = [];
        if (!empty($info)) {
            //判断是否是虚拟订单
            if ($info['is_virtual']) {
                $this->is_virtual = 1;
            } else {
                $this->is_virtual = 0;
            }
            $info["num"] = $data["num"];
            $site_id     = $info['site_id'];
            $price                    = $data["bargain_info"]["curr_price"];
            $goods_money              = $price * $info['num'];
            $info["price"]            = $price;
            $info["goods_money"]      = $goods_money;
            $info['real_goods_money'] = $goods_money;//真实商品金额
            $info['coupon_money']     = 0;//优惠券金额
            $info['promotion_money']  = 0;//优惠金额
            $shop_goods_list['site_id']        = $site_id;
            $shop_goods_list['site_name']      = $info['site_name'];
            $shop_goods_list['goods_money']    = $goods_money;
            $shop_goods_list['goods_list_str'] = $info['sku_id'] . ':' . $info['num'];
            $shop_goods_list['order_name']     = string_split("", ",", $info['sku_name']);
            $shop_goods_list['goods_num']      = $info['num'];
            $shop_goods_list['goods_list'][]   = $info;
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
        if ($this->is_virtual == 1) {
            //虚拟订单  运费为0
            $delivery_money                          = 0;
            $shop_goods['delivery']['delivery_type'] = '';
        } else {
            //查询店铺是否开启快递配送
            $express_config_model         = new ExpressConfig();
            $express_config_result        = $express_config_model->getExpressConfig($site_id);
            $express_config               = $express_config_result["data"];
            $shop_goods["express_config"] = $express_config;
            //查询店铺是否开启门店自提
            $store_config_result        = $express_config_model->getStoreConfig($site_id);
            $store_config               = $store_config_result["data"];
            $shop_goods["store_config"] = $store_config;
            //查询店铺是否开启外卖配送
            $local_config_result        = $express_config_model->getLocalDeliveryConfig($site_id);
            $local_config               = $local_config_result["data"];
            $shop_goods["local_config"] = $local_config;
            //如果本地配送开启, 则查询出本地配送的配置
            if ($shop_goods["local_config"]['is_use'] == 1) {
                $local_model                        = new Local();
                $local_info_result                  = $local_model->getLocalInfo([['site_id', '=', $site_id]]);
                $local_info                         = $local_info_result['data'];
                $shop_goods["local_config"]['info'] = $local_info;
            }
            $delivery_array = $data['delivery'] ?? [];
            $delivery_type  = $delivery_array["delivery_type"] ?? 'express';
            if ($delivery_type == "store") {
                if (isset($data['delivery']["delivery_type"]) && $data['delivery']["delivery_type"] == "store") {
                    //门店自提
                    $delivery_money                          = 0;
                    $shop_goods['delivery']['delivery_type'] = 'store';
                    if ($shop_goods["store_config"]["is_use"] == 0) {
                        $this->error     = 1;
                        $this->error_msg = "门店自提方式未开启!";
                    }
                    if (empty($data['delivery']["store_id"])) {
                        $this->error     = 1;
                        $this->error_msg = "门店未选择!";
                    }
                    $shop_goods['delivery']['store_id'] = $data['delivery']["store_id"];
                    $shop_goods = $this->storeOrderData($shop_goods, $data);
                }
            } else {
                if (empty($data['member_address'])) {
                    $delivery_money                          = 0;
                    $shop_goods['delivery']['delivery_type'] = 'express';
                    $this->error     = 1;
                    $this->error_msg = "未配置默认收货地址!";
                } else {
                    if (!isset($data['delivery']["delivery_type"]) || $data['delivery']["delivery_type"] == "express") {
                        if ($shop_goods["express_config"]["is_use"] == 1) {
                            //物流配送
                            $express            = new Express();
                            $express_fee_result = $express->calculate($shop_goods, $data);
                            if ($express_fee_result["code"] < 0) {
                                $this->error     = 1;
                                $this->error_msg = $express_fee_result["message"];
                                $delivery_fee    = 0;
                            } else {
                                $delivery_fee = $express_fee_result['data']['delivery_fee'];
                            }
                        } else {
                            $this->error     = 1;
                            $this->error_msg = "物流配送方式未开启!";
                            $delivery_fee    = 0;
                        }
                        $delivery_money                          = $delivery_fee;
                        $shop_goods['delivery']['delivery_type'] = 'express';
                    } else if ($data['delivery']["delivery_type"] == "local") {
                        //外卖配送
                        $delivery_money                          = 0;
                        $shop_goods['delivery']['delivery_type'] = 'local';
                        if ($shop_goods["local_config"]["is_use"] == 0) {
                            $this->error     = 1;
                            $this->error_msg = "外卖配送方式未开启!";
                        } else {
                            $local_delivery_time = 0;
                            if (!empty($data['buyer_ask_delivery_time'])) {
                                $buyer_ask_delivery_time_temp = explode(':', $data['buyer_ask_delivery_time']);
                                $local_delivery_time          = $buyer_ask_delivery_time_temp[0] * 3600 + $buyer_ask_delivery_time_temp[1] * 60;
                            }
                            $shop_goods['buyer_ask_delivery_time'] = $local_delivery_time;
                            $local_model  = new Local();
                            $local_result = $local_model->calculate($shop_goods, $data);
                            if ($local_result['code'] < 0) {
                                $this->error     = $local_result['data']['code'];
                                $this->error_msg = $local_result['message'];
                            } else {
                                $delivery_money = $local_result['data']['delivery_money'];
                                if (!empty($local_result['data']['error_code'])) {
                                    $this->error     = $local_result['data']['code'];
                                    $this->error_msg = $local_result['data']['error'];
                                }
                            }
                        }
                    }
                }
            }
        }
        //发票相关
        $shop_goods = $this->invoice($shop_goods, $data);
        $order_money = $goods_money + $delivery_money - $promotion_money - $coupon_money + $shop_goods['invoice_money'] + $shop_goods['invoice_delivery_money'];
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
        $shop_goods['coupon_money']    = $coupon_money;
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
}