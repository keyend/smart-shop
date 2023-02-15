<?php
namespace addon\alipay\event;
use addon\alipay\model\Pay as PayModel;
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
        if ($params["pay_type"] == "alipay") {
            $pay_model = new PayModel($params['site_id']);
            $result    = $pay_model->close($params);
            return $result;
        }
    }
}