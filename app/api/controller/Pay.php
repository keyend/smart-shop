<?php
namespace app\api\controller;
use app\model\system\Pay as PayModel;
/**
 * 支付控制器
 */
class Pay extends BaseApi
{
    /**
     * 支付信息
     */
    public function info()
    {
        $out_trade_no = $this->params['out_trade_no'];
        $pay          = new PayModel();
        $info         = $pay->getPayInfo($out_trade_no);
        return $this->response($info);
    }
    /**
     * 支付调用
     */
    public function pay()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $pay_type     = $this->params['pay_type'];
        $out_trade_no = $this->params['out_trade_no'];
        $app_type     = $this->params['app_type'];
        $return_url   = isset($this->params['return_url']) && !empty($this->params['return_url']) ? urldecode($this->params['return_url']) : null;
        $pay          = new PayModel();
        $info         = $pay->pay($pay_type, $out_trade_no, $app_type, $this->member_id, $return_url);
        return $this->response($info);
    }
    /**
     * 支付方式
     */
    public function type()
    {
        $pay  = new PayModel();
        $info = $pay->getPayType($this->params);
        $temp = empty($info) ? [] : $info;
        $type = [];
        foreach ($temp['data'] as $k => $v) {
            array_push($type, $v["pay_type"]);
        }
        $type = implode(",", $type);
        return $this->response(success(0, '', ['pay_type' => $type]));
    }
    /**
     * 获取订单支付状态
     */
    public function status()
    {
        $pay          = new PayModel();
        $out_trade_no = $this->params['out_trade_no'];
        $res          = $pay->getPayStatus($out_trade_no);
        return $this->response($res);
    }
}