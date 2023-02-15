<?php
namespace addon\seckill\api\controller;
use app\api\controller\BaseApi;
use addon\seckill\model\SeckillOrderCreate as OrderCreateModel;
/**
 * 订单创建
 * @author Administrator
 *
 */
class Ordercreate extends BaseApi
{
    /**
     * 创建订单
     */
    public function create()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_create = new OrderCreateModel();
        $data         = [
            'id'              => isset($this->params['seckill_goods_id']) ? $this->params['seckill_goods_id'] : '',//秒杀商品id
            'num'             => isset($this->params['num']) ? $this->params['num'] : 1,//秒杀商品数量(买几套)
            'member_id'       => $this->member_id,
            'site_id'         => $this->site_id,//站点id
            'is_balance'      => isset($this->params['is_balance']) ? $this->params['is_balance'] : 0,//是否使用余额
            'pay_password'    => isset($this->params['pay_password']) ? $this->params['pay_password'] : '',//支付密码
            'order_from'      => $this->params['app_type'],
            'order_from_name' => $this->params['app_type_name'],
            'buyer_message'   => $this->params['buyer_message'] ?? '',
            'delivery'        => isset($this->params["delivery"]) && !empty($this->params["delivery"]) ? json_decode($this->params["delivery"], true) : [],
            'member_address'  => isset($this->params["member_address"]) && !empty($this->params["member_address"]) ? json_decode($this->params["member_address"], true) : [],
            'latitude'  => $this->params["latitude"] ?? null,
            'longitude' => $this->params["longitude"] ?? null,
            'is_invoice'              => $this->params["is_invoice"] ?? 0,
            'invoice_type'            => $this->params["invoice_type"] ?? 0,
            'invoice_title'           => $this->params["invoice_title"] ?? '',
            'taxpayer_number'         => $this->params["taxpayer_number"] ?? '',
            'invoice_content'         => $this->params["invoice_content"] ?? '',
            'invoice_full_address'    => $this->params["invoice_full_address"] ?? '',
            'is_tax_invoice'          => $this->params["is_tax_invoice"] ?? 0,
            'invoice_email'           => $this->params["invoice_email"] ?? '',
            'invoice_title_type'      => $this->params["invoice_title_type"] ?? 0,
            'buyer_ask_delivery_time' => $this->params["buyer_ask_delivery_time"] ?? '',
        ];
        if (empty($data['id'])) {
            return $this->response($this->error('', '缺少必填参数商品数据'));
        }
        $res = $order_create->create($data);
        return $this->response($res);
    }
    /**
     * 计算信息
     */
    public function calculate()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_create = new OrderCreateModel();
        $data         = [
            'id'              => isset($this->params['seckill_goods_id']) ? $this->params['seckill_goods_id'] : '',//秒杀商品id
            'num'             => isset($this->params['num']) ? $this->params['num'] : 1,//秒杀商品数量(买几套)
            'member_id'       => $this->member_id,
            'site_id'         => $this->site_id,//站点id
            'is_balance'      => isset($this->params['is_balance']) ? $this->params['is_balance'] : 0,//是否使用余额
            'order_from'      => $this->params['app_type'],
            'order_from_name' => $this->params['app_type_name'],
            'delivery'        => isset($this->params["delivery"]) && !empty($this->params["delivery"]) ? json_decode($this->params["delivery"], true) : [],
            'member_address'  => isset($this->params["member_address"]) && !empty($this->params["member_address"]) ? json_decode($this->params["member_address"], true) : [],
            'latitude'  => $this->params["latitude"] ?? null,
            'longitude' => $this->params["longitude"] ?? null,
            'is_invoice'              => $this->params["is_invoice"] ?? 0,
            'invoice_type'            => $this->params["invoice_type"] ?? 0,
            'invoice_title'           => $this->params["invoice_title"] ?? '',
            'taxpayer_number'         => $this->params["taxpayer_number"] ?? '',
            'invoice_content'         => $this->params["invoice_content"] ?? '',
            'invoice_full_address'    => $this->params["invoice_full_address"] ?? '',
            'is_tax_invoice'          => $this->params["is_tax_invoice"] ?? 0,
            'invoice_email'           => $this->params["invoice_email"] ?? '',
            'invoice_title_type'      => $this->params["invoice_title_type"] ?? 0,
            'buyer_ask_delivery_time' => $this->params["buyer_ask_delivery_time"] ?? '',
        ];
        if (empty($data['id'])) {
            return $this->response($this->error('', '缺少必填参数商品数据'));
        }
        $res = $order_create->calculate($data);
        return $this->response($this->success($res));
    }
    /**
     * 待支付订单 数据初始化
     * @return string
     */
    public function payment()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_create = new OrderCreateModel();
        $data         = [
            'id'              => isset($this->params['seckill_goods_id']) ? $this->params['seckill_goods_id'] : '',//秒杀商品id
            'num'             => isset($this->params['num']) ? $this->params['num'] : 1,//秒杀商品数量(买几套)
            'member_id'       => $this->member_id,
            'site_id'         => $this->site_id,//站点id
            'is_balance'      => isset($this->params['is_balance']) ? $this->params['is_balance'] : 0,//是否使用余额
            'order_from'      => $this->params['app_type'],
            'order_from_name' => $this->params['app_type_name'],
            'latitude'         => $this->params["latitude"] ?? null,
            'longitude'        => $this->params["longitude"] ?? null,
            'default_store_id' => $this->params["default_store_id"] ?? 0,
        ];
        if (empty($data['id'])) {
            return $this->response($this->error('', '缺少必填参数商品数据'));
        }
        $res = $order_create->orderPayment($data);
        return $this->response($res);
    }
}