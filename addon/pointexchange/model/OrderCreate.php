<?php
namespace addon\pointexchange\model;
use app\model\BaseModel;
use addon\coupon\model\Coupon;
use app\model\express\Express;
use app\model\goods\Goods;
use app\model\goods\GoodsStock;
use app\model\member\Member;
use app\model\member\MemberAccount;
use app\model\member\MemberAddress;
use app\model\store\Store;
use app\model\system\Pay;
use think\facade\Cache;
use app\model\system\Cron;
use app\model\order\Config;
use \app\model\order\OrderCreate as OrderCreateModel;
use app\model\express\Config as ExpressConfig;
use app\model\express\Local;
/**
 * 积分兑换
 */
class OrderCreate extends BaseModel
{
    private $error = 0;  //是否有错误
    private $error_msg = '';  //错误描述
    private $member_balance_money = 0; //会员账户余额(计算过程中会逐次减少)
    /**
     * 创建订单
     * @param $data
     */
    public function create($data)
    {
        $calculate_data = $this->calculate($data);//计算并查询套餐信息
        if (isset($calculate_data['code']) && $calculate_data['code'] < 0)
            return $calculate_data;
        if ($this->error > 0) {
            return $this->error(['error_code' => $this->error], $this->error_msg);
        }
        $pay_model = new Pay();
        $out_trade_no = $pay_model->createOutTradeNo();
        model("promotion_exchange_order")->startTrans();
        try {
            $order_no = $this->createOrderNo($calculate_data["site_id"]);
            $exchange_info = $calculate_data["exchange_info"] ?? [];
            $delivery_type = $data['delivery']['delivery_type'] ?? '';
            $delivery_type_name = Express::express_type[$delivery_type]["title"] ?? '';
            //创建普通订单
            $order_id = 0;
            $order_data = array(
                "order_no" => $order_no,
                "member_id" => $data["member_id"],
                "out_trade_no" => $out_trade_no,
                "point" => $calculate_data["point"],
                "exchange_price" => $exchange_info["price"],
                "delivery_price" => $calculate_data["delivery_money"],
                "price" => $calculate_data["order_money"],
                "create_time" => time(),
                "exchange_id" => $exchange_info["id"],
                "exchange_name" => $exchange_info["name"],
                "exchange_image" => $exchange_info["image"],
                "num" => $calculate_data["num"],
                "order_status" => 0,
                "type" => $exchange_info["type"],
                "type_name" => $exchange_info["type_name"],
                'name' => $calculate_data['member_address']['name'] ?? '',
                'mobile' => $calculate_data['member_address']['mobile'] ?? '',
                'telephone' => $calculate_data['member_address']['telephone'] ?? '',
                'province_id' => $calculate_data['member_address']['province_id'] ?? '',
                'city_id' => $calculate_data['member_address']['city_id'] ?? '',
                'district_id' => $calculate_data['member_address']['district_id'] ?? '',
                'community_id' => $calculate_data['member_address']['community_id'] ?? '',
                'address' => $calculate_data['member_address']['address'] ?? '',
                'full_address' => $calculate_data['member_address']['full_address'] ?? '',
                'longitude' => $calculate_data['member_address']['longitude'] ?? '',
                'latitude' => $calculate_data['member_address']['latitude'] ?? '',
                'order_from' => $data['order_from'],
                'order_from_name' => $data['order_from_name'],
                "buyer_message" => $calculate_data["buyer_message"],
                "type_id" => $exchange_info["type_id"],
                "pay_money" => $calculate_data['pay_money'],
                "balance_money" => $calculate_data['balance_money'],
                "balance" => $calculate_data["balance"],
                "site_id" => $calculate_data["site_id"],
                'order_id' => $order_id,
                'delivery_type' => $delivery_type,
                'delivery_type_name' => $delivery_type_name,
                'delivery_status' => $calculate_data["delivery_status"] ?? 0,
                'delivery_status_name' => $calculate_data["delivery_status_name"] ?? '',
                'delivery_store_id' => $calculate_data["delivery_store_id"] ?? 0,
                "delivery_store_name" => $calculate_data["delivery_store_name"] ?? '',
                "delivery_store_info" => $calculate_data["delivery_store_info"] ?? '',
                'buyer_ask_delivery_time' => $calculate_data['buyer_ask_delivery_time'] ?? '',//定时达
            );
            $order_id = model("promotion_exchange_order")->add($order_data);
            //判断库存
            $exchange_model = new Exchange();
            //减去套餐的库存
            $exchange_result = $exchange_model->decStock(["id" => $exchange_info["id"], "num" => $calculate_data["num"]]);
            if ($exchange_result['code'] < 0) {
                model("promotion_exchange_order")->rollback();
                return $exchange_result;
            }
            switch ($exchange_info["type"]) {//兑换类型
                case "1"://商品
                    //库存变化
                    $goods_stock_model = new GoodsStock();
                    $result = $goods_stock_model->decStock(["sku_id" => $exchange_info['type_id'], "num" => $calculate_data["num"]]);
                    break;
                case "2"://优惠券
                    $coupon_model = new Coupon();
                    $result = $coupon_model->decStock(["coupon_type_id" => $exchange_info["type_id"], "num" => $calculate_data["num"]]);
                    break;
                case "3":
                default:
                    $result = $this->success();
                    break;
            }
            if ($result["code"] < 0) {
                model("promotion_exchange_order")->rollback();
                return $result;
            }
            //扣除积分
            $member_account_model = new MemberAccount();
            $member_account_result = $member_account_model->addMemberAccount($exchange_info['site_id'], $data["member_id"], "point", -$calculate_data["point"], "order", "积分兑换", "积分兑换,扣除积分:" . $calculate_data["point"]);
            if ($member_account_result["code"] < 0) {
                model("promotion_exchange_order")->rollback();
                return $member_account_result;
            }
            //扣除余额(统一扣除)
            if ($calculate_data["balance_money"] > 0) {
                $balance_result = $this->useBalance($calculate_data, $data['site_id']);
                if ($balance_result["code"] < 0) {
                    model("order")->rollback();
                    return $balance_result;
                }
            }
            //生成整体支付单据
            //商品兑换  实际生成普通订单
            $pay_model = new Pay();
            $pay_model->addPay($calculate_data["site_id"], $out_trade_no, "POINT", $calculate_data["order_name"], $calculate_data["order_name"], $calculate_data["pay_money"], '', 'PointexchangeOrderPayNotify', '');
            model("promotion_exchange_order")->commit();
            return $this->success($out_trade_no);
        } catch (\Exception $e) {
            model("promotion_exchange_order")->rollback();
            return $this->error('', $e->getMessage() . $e->getFile() . $e->getLine());
        }
    }
    /**
     * 使用余额
     * @param $data
     * @param $site_id
     * @return array
     */
    public function useBalance($data, $site_id)
    {
        $this->pay_type = "BALANCE";
        $member_model   = new Member();
        $result         = $member_model->checkPayPassword($data["member_id"], $data["pay_password"]);
        if ($result["code"] >= 0) {
            $balance_money        = $data["member_account"]["balance_money"]; //不可提现余额
            $balance              = $data["member_account"]["balance"]; //可提现余额
            $member_account_model = new MemberAccount();
            $surplus_banance      = $data["balance_money"];
            //优先扣除不可提现余额
            if ($balance > 0) {
                if ($balance >= $surplus_banance) {
                    $real_balance = $surplus_banance;
                } else {
                    $real_balance = $balance;
                }
                $result          = $member_account_model->addMemberAccount($site_id, $data["member_id"], "balance", -$real_balance, "order", "余额抵扣", "订单余额抵扣,扣除不可提现余额:" . $real_balance);
                $surplus_banance -= $real_balance;
            }
            if ($surplus_banance > 0) {
                $result = $member_account_model->addMemberAccount($site_id, $data["member_id"], "balance_money", -$surplus_banance, "order", "余额抵扣", "订单余额抵扣,扣除可提现余额:" . $surplus_banance);
            }
            return $result;
        } else {
            return $result;
        }
    }
    /**
     * 初始化会员账户
     * @param $data
     * @return mixed
     */
    public function initMemberAccount($data)
    {
        $member_model       = new Member();
        $member_info_result = $member_model->getMemberDetail($data["member_id"]);
        $member_info        = $member_info_result["data"];
        if (!empty($member_info)) {
            if (!empty($member_info["pay_password"])) {
                $is_pay_password = 1;
            } else {
                $is_pay_password = 0;
            }
            unset($member_info["pay_password"]);
            $member_info["is_pay_password"] = $is_pay_password;
            $data['member_account']         = $member_info;
        }
        return $data;
    }
    /**
     * 待支付订单
     * @param $data
     */
    public function payment($data)
    {
        $calculate_data = $this->calculate($data);//计算并查询套餐信息
        if (isset($calculate_data['code']) && $calculate_data['code'] < 0)
            return $calculate_data;
        $express_type = [];
        if ($calculate_data['exchange_info']['type'] == 1 && $calculate_data['is_virtual'] == 0) {
            if (!empty($calculate_data['member_address'])) {
                //2. 查询店铺配送方式（1. 物流  2. 自提  3. 外卖）
                if ($calculate_data["express_config"]["is_use"] == 1) {
                    $express_type[] = ["title" => Express::express_type["express"]["title"], "name" => "express"];
                }
                //查询店铺是否开启门店自提
                if ($calculate_data["store_config"]["is_use"] == 1) {
                    //根据坐标查询门店
                    $store_model = new Store();
                    $store_condition = array(
                        ['site_id', '=', $data['site_id']],
                        ['is_pickup', '=', 1],
                        ['status', '=', 1],
                        ['is_frozen', '=', 0],
                    );
                    $latlng = array(
                        'lat' => $data['latitude'],
                        'lng' => $data['longitude'],
                    );
                    $store_list_result = $store_model->getLocationStoreList($store_condition, '*', $latlng);
                    $store_list = $store_list_result["data"];
                    //如果用户默认选中了门店
                    $store_id = 0;
                    if ($calculate_data['default_store_id'] > 0) {
                        if (!empty($store_list)) {
                            $store_array = array_column($store_list, 'store_id');
                            if (in_array($calculate_data['default_store_id'], $store_array)) {
                                $store_id = $calculate_data['default_store_id'];
                            } else {
                                $store_condition = array(
                                    ['site_id', '=', $calculate_data['site_id']],
                                    ['is_pickup', '=', 1],
                                    ['status', '=', 1],
                                    ['is_frozen', '=', 0],
                                    ['store_id', '=', $calculate_data['default_store_id']],
                                );
                                $store_info_result = $store_model->getStoreInfo($store_condition, '*');
                                $store_info = $store_info_result['data'];
                                if (!empty($store_info)) {
                                    $store_id = $calculate_data['default_store_id'];
                                    $store_list[] = $store_info;
                                }
                            }
                        }
                    }
                    $express_type[] = ["title" => Express::express_type["store"]["title"], "name" => "store", "store_list" => $store_list, 'store_id' => $store_id];
                }
                //查询店铺是否开启外卖配送
                if ($calculate_data["local_config"]["is_use"] == 1) {
                    //查询本店的通讯地址
                    $express_type[] = ["title" => "外卖配送", "name" => "local"];
                }
            }
        }
        $calculate_data["express_type"] = $express_type;
        return $this->success($calculate_data);
    }
    /**
     * 计算
     * @param $data
     */
    public function calculate($data)
    {
        $data = $this->initMemberAddress($data);
        $data = $this->initMemberAccount($data);
        //余额付款
        if ($data['is_balance'] > 0) {
            $this->member_balance_money = $data["member_account"]["balance_total"] ?? 0;
        }
        //交易配置
        $config_model = new Config();
        $order_config_result = $config_model->getOrderEventTimeConfig($data['site_id']);
        $order_config = $order_config_result["data"];
        $data['order_config'] = $order_config['value'] ?? [];
        $id = $data["id"];
        $exchange_model = new Exchange();
        $exchange_info_result = $exchange_model->getExchangeInfo($id, 'id,site_id,type,type_name,type_id,name,image,stock,pay_type,point,market_price,price,limit_num,delivery_price,balance,state');
        $exchange_info = $exchange_info_result["data"];
        if (empty($exchange_info))
            return $this->error('', "找不到对应的积分兑换活动!");
        $data["exchange_info"] = $exchange_info;
        if ($exchange_info["state"] == 0) {
            $this->error = 1;
            $this->error_msg = "当前兑换活动未开启!";
        }
        if ($exchange_info["type"] == 1 && $exchange_info["limit_num"] > 0) {
            // 已兑换数量
            $exchangeed_num = model("promotion_exchange_order")->getSum([ ['exchange_id', '=', $exchange_info['id'] ], ['order_status', '<>', '-1'], ['member_id', '=', $data['member_id'] ] ], 'num');
            if (($exchangeed_num + $data['num']) > $exchange_info["limit_num"]) {
                $this->error = 1;
                $this->error_msg = "兑换数量已超出该兑换活动的兑换限制!";
            }
        }
        if ($exchange_info["stock"] <= 0) {
            $this->error = 1;
            $this->error_msg = "当前兑换库存不足!";
        }
        $delivery_status = 0;//发货状态
        $delivery_status_name = '';//发货状态名称
        //兑换类型为1时   兑换物品为商品(相对优惠券和红包来说较为特殊)
        if ($exchange_info['type'] == 1) {
            $goods_model = new Goods();
            $goods_result = $goods_model->getGoodsSkuInfo([['sku_id', '=', $exchange_info['type_id']], ['site_id', '=', $data['site_id']]], '*');
            $goods_info = $goods_result['data'];
            $data['goods_info'] = $goods_info;
            $data['is_virtual'] = $goods_info['is_virtual'];
            //计算邮费
            if ($data['is_virtual'] == 1) {
                //虚拟订单  运费为0
                $data['delivery']['delivery_type'] = '';
            } else {
                $delivery_status = 0;//发货状态
                $delivery_status_name = '待发货';//发货状态名称
                //查询店铺是否开启快递配送
                $express_config_model = new ExpressConfig();
                $express_config_result = $express_config_model->getExpressConfig($data['site_id']);
                $express_config = $express_config_result["data"];
                $data["express_config"] = $express_config;
                //查询店铺是否开启门店自提
                $store_config_result = $express_config_model->getStoreConfig($data['site_id']);
                $store_config = $store_config_result["data"];
                $data["store_config"] = $store_config;
                //查询店铺是否开启外卖配送
                $local_config_result = $express_config_model->getLocalDeliveryConfig($data['site_id']);
                $local_config = $local_config_result["data"];
                $data["local_config"] = $local_config;
                //如果本地配送开启, 则查询出本地配送的配置
                if ($data["local_config"]['is_use'] == 1) {
                    $local_model = new Local();
                    $local_info_result = $local_model->getLocalInfo([['site_id', '=', $data['site_id']]]);
                    $local_info = $local_info_result['data'];
                    $data["local_config"]['info'] = $local_info;
                }
                $delivery_array = $data['delivery'] ?? [];
                $delivery_type = $delivery_array["delivery_type"] ?? 'express';
                if ($delivery_type == "store") {
                    //门店自提
                    $data['delivery']['delivery_type'] = 'store';
                    if ($data["store_config"]["is_use"] == 0) {
                        $this->error = 1;
                        $this->error_msg = "门店自提方式未开启!";
                    }
                    if (empty($data['delivery']["store_id"])) {
                        $this->error = 1;
                        $this->error_msg = "门店未选择!";
                    }
                    $data['delivery']['store_id'] = $data['delivery']["store_id"];
                    //订单自提的信息补充
                    $order_create_model = new OrderCreateModel();
                    $data = $order_create_model->storeOrderData($data, $data);
                } else {
                    if (empty($data['member_address'])) {
                        $data['delivery']['delivery_type'] = 'express';
                        $this->error = 1;
                        $this->error_msg = "未配置默认收货地址!";
                    } else {
                        if (!isset($data['delivery']["delivery_type"]) || $data['delivery']["delivery_type"] == "express") {
                            if ($data["express_config"]["is_use"] == 1) {
                                //物流配送
//                                $express = new Express();
//                                $express_result = $express->isSupportDelivery($data['member_address']['district_id'], $data['site_id']);
//                                if ($express_result["code"] < 0) {
//                                    $this->error = 1;
//                                    $this->error_msg = $express_result["message"];
//
//                                }
                            } else {
                                $this->error = 1;
                                $this->error_msg = "物流配送方式未开启!";
                            }
                            $data['delivery']['delivery_type'] = 'express';
                        } else if ($data['delivery']["delivery_type"] == "local") {
                            //外卖配送
                            $data['delivery']['delivery_type'] = 'local';
                            if ($data["local_config"]["is_use"] == 0) {
                                $this->error = 1;
                                $this->error_msg = "外卖配送方式未开启!";
                            } else {
                                $local_delivery_time = 0;
                                if (!empty($data['buyer_ask_delivery_time'])) {
                                    $buyer_ask_delivery_time_temp = explode(':', $data['buyer_ask_delivery_time']);
                                    $local_delivery_time = $buyer_ask_delivery_time_temp[0] * 3600 + $buyer_ask_delivery_time_temp[1] * 60;
                                }
                                $data['buyer_ask_delivery_time'] = $local_delivery_time;
                                $local_model = new Local();
                                $local_result = $local_model->isSupportDelivery($data);
                                if ($local_result['code'] < 0) {
                                    $this->error = $local_result['data']['code'];
                                    $this->error_msg = $local_result['message'];
                                } else {
                                    if (!empty($local_result['data']['error_code'])) {
                                        $this->error = $local_result['data']['code'];
                                        $this->error_msg = $local_result['data']['error'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $data['delivery_status'] = $delivery_status;
        $data['delivery_status_name'] = $delivery_status_name;
        $point = $exchange_info["point"];
        $price = $exchange_info["price"];
        $balance = $exchange_info["balance"];
        $gooods_num = $data["num"];
        $goods_money = $price * $data["num"];
        $delivery_money = 0;//运费
        $order_money = $goods_money + $delivery_money;
        $order_name = $exchange_info["name"] . "【" . $exchange_info["type_name"] . "】";
        //抵扣余额
        $balance_money = 0;
        //余额抵扣(判断是否使用余额)
        if ($this->member_balance_money > 0) {
            $temp_order_money = $order_money;
            if ($temp_order_money <= $this->member_balance_money) {
                $balance_money = $temp_order_money;
            } else {
                $balance_money = $this->member_balance_money;
            }
        } else {
            $balance_money = 0;
        }
        //计算出实际支付金额
        $pay_money = $order_money - $balance_money;
        $data['point'] = $point * $data["num"];
        $data['price'] = $price * $data["num"];
        $data['goods_num'] = $gooods_num;
        $data['order_name'] = $order_name;
        $data['order_money'] = $order_money;
        $data['pay_money'] = $pay_money;
        $data['balance_money'] = $balance_money;
        $data['balance'] = $balance * $data["num"];
        $data['delivery_money'] = $delivery_money;
        return $data;
    }
    /**
     * 初始化收货地址
     * @param unknown $data
     */
    public function initMemberAddress($data)
    {
        //收货人地址管理
        if (empty($data['member_address'])) {
            $member_address = new MemberAddress();
            $address = $member_address->getMemberAddressInfo([['member_id', '=', $data['member_id']], ['is_default', '=', 1]]);
            $data['member_address'] = $address['data'];
        }
        return $data;
    }
    /**
     * 增加订单自动关闭事件
     * @param $order_id
     * @param $site_id
     */
    public function addOrderCronClose($order_id, $site_id)
    {
        //计算订单自动关闭时间
        $config_model = new Config();
        $order_config_result = $config_model->getOrderEventTimeConfig($site_id);
        $order_config = $order_config_result["data"];
        $now_time = time();
        if (!empty($order_config)) {
            $execute_time = $now_time + $order_config["value"]["auto_close"] * 60;//自动关闭时间
        } else {
            $execute_time = $now_time + 3600;//尚未配置  默认一天
        }
        $cron_model = new Cron();
        $cron_model->addCron(1, 0, "积分兑换订单自动关闭", "CronExchangeOrderClose", $execute_time, $order_id);
    }
    /**
     * 验证订单支付金额知否为0  如果为0  立即支付完成
     * @param $order_data
     */
    public function checkFree($order_data)
    {
        if ($order_data["price"] == 0) {
            $pay_model = new Pay();
            $pay_model->onlinePay($order_data["out_trade_no"], "ONLINE_PAY", '', '');
        }
    }
    /**
     * 生成订单编号
     *
     * @param array $site_id
     */
    public function createOrderNo($site_id)
    {
        $time_str = date('YmdHi');
        $num = 0;
        $max_no = Cache::get($site_id . "_" . $time_str);
        if (!isset($max_no) || empty($max_no)) {
            $max_no = 1;
        } else {
            $max_no = $max_no + 1;
        }
        $order_no = $time_str . sprintf("%04d", $max_no);
        Cache::set($site_id . "_" . $time_str, $max_no);
        return $order_no;
    }
}