<?php
namespace addon\alipay\event;
use addon\alipay\model\Pay as PayModel;
use app\model\system\Pay as PayCommon;
/**
 * 支付回调
 */
class PayNotify
{
    /**
     * 支付方式及配置
     */
    public function handle()
    {
        if (isset($_POST['out_trade_no'])) {
            $out_trade_no = $_POST['out_trade_no'];
            $pay          = new PayCommon();
            $pay_info     = $pay->getPayInfo($out_trade_no);
            $pay_info     = $pay_info['data'];
            if (!empty($pay_info)) {
                $pay_model = new PayModel($pay_info['site_id']);
                $pay_model->payNotify();
            }
        }
    }
}