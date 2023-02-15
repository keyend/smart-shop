<?php
namespace addon\alipay\event;
use addon\alipay\model\Pay as PayModel;
/**
 * 生成支付
 */
class Pay
{
    /**
     * 支付方式及配置
     */
    public function handle($param)
    {
        if ($param["pay_type"] == "alipay") {
            if (in_array($param["app_type"], ["h5", "app", "pc", "aliapp"])) {
                $pay_model = new PayModel($param['site_id']);
                $res       = $pay_model->pay($param);
                return $res;
            }
        }
    }
}