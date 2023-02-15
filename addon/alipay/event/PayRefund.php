<?php
namespace addon\alipay\event;
use addon\alipay\model\Pay as PayModel;
/**
 * 原路退款
 */
class PayRefund
{
    /**
     * 关闭支付
     */
    public function handle($params)
    {
        if ($params["pay_info"]["pay_type"] == "alipay") {
            $pay_model = new PayModel($params['site_id']);
            $result    = $pay_model->refund($params);
            return $result;
        }
    }
}