<?php
namespace addon\unionwxpay\event;
use addon\unionwxpay\model\Pay as PayModel;
/**
 * 原路退款
 */
class PayRefund
{
    /**
     * 原路退款
     */
    public function handle($params)
    {
        if ($params["pay_info"]["pay_type"] == "unionwxpay") {
            $pay_model = new PayModel(0, $params['site_id']);
            $result    = $pay_model->refund($params);
            return $result;
        }
    }
}