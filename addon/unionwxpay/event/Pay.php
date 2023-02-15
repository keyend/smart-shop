<?php
namespace addon\unionwxpay\event;
use addon\unionwxpay\model\Pay as PayModel;
/**
 * 生成支付
 */
class Pay
{
    /**
     * 支付
     */
    public function handle($params)
    {
        if ($params["pay_type"] == "unionwxpay") {
            $app_type = $params['app_type'];
            $is_weapp = 0;
            switch ($app_type) {
                case 'wechat' :
                    $trade_type = "JSAPI";
                    break;
                case 'weapp' :
                    $is_weapp   = 1;
                    $trade_type = "JSAPI";
                    break;
            }
            $params["trade_type"] = $trade_type;
            $pay_model            = new PayModel($is_weapp, $params['site_id']);
            $result               = $pay_model->pay($params);
            return $result;
        }
    }
}