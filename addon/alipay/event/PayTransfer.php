<?php
namespace addon\alipay\event;
use addon\alipay\model\Pay;
class PayTransfer
{
    public function handle(array $params)
    {
        if ($params['transfer_type'] == 'alipay') {
            $pay = new Pay($params['site_id']);
            $res = $pay->payTransfer($params);
            return $res;
        }
    }
}