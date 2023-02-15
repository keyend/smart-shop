<?php
namespace addon\unionwxpay\event;
use addon\unionwxpay\model\Pay as PayModel;
/**
 * 关闭支付
 */
class PayClose
{
    /**
     * 关闭支付
     */
    public function handle($params)
    {
        if ($params["pay_type"] == "unionwxpay") {
            $pay_model = new PayModel(0, $params['site_id']);
            $result    = $pay_model->close($params);
            return $result;
        }
    }
}