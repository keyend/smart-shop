<?php
namespace addon\wechatpay\event;
use addon\wechatpay\model\Pay;
class PayTransfer
{
    public function handle(array $params)
    {
        if ($params['transfer_type'] == 'wechatpay') {
            $is_weapp = $params['is_weapp'] ?? 0;
            $pay      = new Pay($is_weapp, $params['site_id']);
            $res      = $pay->transfer($params);
            return $res;
        }
    }
}