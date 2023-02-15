<?php
namespace addon\memberrecharge\model;
use app\model\order\OrderCreate;
use app\model\system\Cron;
use app\model\system\Pay;
use app\model\order\Config;
use app\model\member\Member as MemberModel;
/**
 * 订单创建(拼团)
 *
 * @author Administrator
 *
 */
class MemberrechargeOrderCreate extends OrderCreate
{
    /**
     * 订单创建
     * @param unknown $data
     */
    public function create($data)
    {
        //获取用户头像
        $member_model = new MemberModel();
        $member       = $member_model->getMemberInfo([['member_id', '=', $data['member_id']]], 'headimg,nickname');
        $member_info  = $member['data'];
        //获取套餐信息
        $recharge_model = new Memberrecharge();
        if ($data['recharge_id'] > 0) {
            //套餐字段
            $field         = 'recharge_id,recharge_name,cover_img,face_value,buy_price,point,growth,coupon_id';
            $recharge      = $recharge_model->getMemberrechargeInfo([['recharge_id', '=', $data['recharge_id']]], $field);
            $recharge_info = $recharge['data'];
            if (empty($recharge_info)) {
                return $this->error('', '无效的充值套餐');
            }
        } else {
            $recharge_info = array(
                "recharge_id"   => 0,
                "recharge_name" => '自定义面额充值',
                "cover_img"     => '',
                "face_value"    => $data['face_value'],
                "buy_price"     => $data['face_value'],
                "point"         => 0,
                "growth"        => 0,
                "coupon_id"     => 0,
            );
        }
        //创建或加入
        $pay          = new Pay();
        $out_trade_no = $pay->createOutTradeNo();
        $order_no     = $this->createOrderNo($data['site_id']);
        $order_data = [
            'recharge_id'     => $data['recharge_id'],
            'order_no'        => $order_no,
            'out_trade_no'    => $out_trade_no,
            'recharge_name'   => $recharge_info['recharge_name'],
            'cover_img'       => $recharge_info['cover_img'],
            'face_value'      => $recharge_info['face_value'],
            'buy_price'       => $recharge_info['buy_price'],
            'point'           => $recharge_info['point'],
            'growth'          => $recharge_info['growth'],
            'coupon_id'       => $recharge_info['coupon_id'],
            'status'          => 1,
            'create_time'     => time(),
            'member_id'       => $data['member_id'],
            'member_img'      => $member_info['headimg'],
            'nickname'        => $member_info['nickname'],
            'order_from'      => $data['order_from'],
            'order_from_name' => $data['order_from_name'],
            'site_id'         => $data['site_id']
        ];
        model("member_recharge_order")->startTrans();
        //循环生成多个订单
        try {
            $order_id = model("member_recharge_order")->add($order_data);
            //生成整体支付单据
            $pay->addPay($data['site_id'], $out_trade_no, "", "会员充值套餐，面额：" . $recharge_info['face_value'], "会员充值套餐，面额：" . $recharge_info['face_value'], $recharge_info['buy_price'], '', 'MemberrechargeOrderPayNotify', '');
            //计算订单自动关闭时间
            $config_model        = new Config();
            $order_config_result = $config_model->getOrderEventTimeConfig($data['site_id']);
            $order_config        = $order_config_result["data"];
            $now_time            = time();
            if (!empty($order_config)) {
                $execute_time = $now_time + $order_config["value"]["auto_close"] * 60;//自动关闭时间
            } else {
                $execute_time = $now_time + 3600;//尚未配置  默认一天
            }
            $cron_model = new Cron();
            $cron_model->addCron(1, 0, "订单自动关闭", "MemberrechargeOrderClose", $execute_time, $order_id);
            event("MemberRechargeOrderCreate", ['order_id' => $order_id]);
            model("member_recharge_order")->commit();
            return $this->success($out_trade_no);
        } catch (\Exception $e) {
            model("member_recharge_order")->rollback();
            return $this->error('', $e->getMessage());
        }
    }
}